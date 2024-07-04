<?php

declare(strict_types=1);

namespace App\Services\Addresses\Aggregates;

use App\Facades\Network;
use App\Models\Wallet;
use App\Services\Cache\StatisticsCache;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class LatestWalletAggregate
{
    public function aggregate(): ?Wallet
    {
        $cacheKey = 'commands:cache_address_statistics/last_run';

        $newestQuery = Wallet::query()
            ->select('wallets.address', DB::raw('MIN(transactions.timestamp) as timestamp'))
            ->leftJoin('transactions', 'wallets.address', '=', 'transactions.recipient_id')
            ->whereNotNull('transactions.sender_public_key')
            ->whereNotNull('transactions.recipient_id')
            ->groupBy('wallets.address')
            ->orderBy('timestamp', 'desc')
            ->limit(1);

        $newestMultipaymentQuery = Wallet::query()
            ->select('wallets.address', DB::raw('MIN(transactions.timestamp) as timestamp'))
            ->join(DB::raw(
                '(select
                    "t"."id",
                    "t"."timestamp",
                    jsonb_array_elements("t".asset->\'payments\')->>\'recipientId\' as multipayment_recipient
                from
                    transactions "t") "transactions"'
            ), 'multipayment_recipient', '=', 'wallets.address', 'left')
            ->whereNotNull('transactions.multipayment_recipient')
            ->groupBy('wallets.address')
            ->orderBy('timestamp', 'desc')
            ->limit(1);

        $lastRun = Cache::get($cacheKey, null);
        if ($lastRun !== null) {
            $newestQuery->whereRaw(sprintf('timestamp + %d > ?', Network::epoch()->timestamp), [$lastRun->timestamp]);
            $newestMultipaymentQuery->whereRaw(sprintf('timestamp + %d > ?', Network::epoch()->timestamp), [$lastRun->timestamp]);
        }

        $newest             = null;
        $newestStandard     = $newestQuery->first();
        $newestMultipayment = $newestMultipaymentQuery->first();
        // dd($newestStandard, $newestMultipayment);
        if ($newestStandard === null && $newestMultipayment === null) {
            return null;
        } elseif ($newestStandard !== null && $newestMultipayment === null) {
            $newest = $newestStandard;
        } elseif ($newestStandard === null && $newestMultipayment !== null) {
            $newest = $newestMultipayment;
        } elseif ($newestStandard->timestamp > $newestMultipayment->timestamp) {
            $newest = $newestStandard;
        } else {
            $newest = $newestMultipayment;
        }

        if ($newest === null) {
            return null;
        }

        // Cache last run timestamp for newest wallet query
        Cache::rememberForever($cacheKey, function () {
            return Carbon::now();
        });

        $cache = new StatisticsCache();
        $cache->setNewestAddress([
            'address'   => $newest->address,
            'timestamp' => $newest->timestamp,
            'value'     => Carbon::createFromTimestamp((int) $newest->timestamp + (int) Network::epoch()->timestamp)->format(DateFormat::DATE),
        ]);

        return $newest;
    }
}
