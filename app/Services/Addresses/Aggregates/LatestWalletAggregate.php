<?php

declare(strict_types=1);

namespace App\Services\Addresses\Aggregates;

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
        $cacheKey = 'commands:cache_address_statistics/last_updated_at_height';

        $newestQuery = Wallet::query()
            ->select(
                'wallets.address',
                DB::raw('MIN(transactions.timestamp) as timestamp'),
                DB::raw('MAX(wallets.updated_at) as updated_at'),
            )
            ->leftJoin('transactions', function ($join) {
                $join->on('wallets.public_key', '=', 'transactions.sender_public_key')
                    ->orOn('wallets.address', '=', 'transactions.recipient_address');
            })
            ->whereNotNull('transactions.sender_public_key')
            ->whereNotNull('transactions.recipient_address')
            ->groupBy('wallets.address')
            ->orderBy('timestamp', 'desc')
            ->limit(1);

        $lastRun = Cache::get($cacheKey, null);
        if ($lastRun !== null) {
            $newestQuery->where('wallets.updated_at', '>', $lastRun);
        }

        $newest = $newestQuery->first();
        if ($newest === null) {
            return null;
        }

        $cache = new StatisticsCache();

        $currentAddress = $cache->getNewestAddress();
        if ($currentAddress !== null && $currentAddress['timestamp'] > $newest->timestamp) {
            return null;
        }

        $cache->setNewestAddress([
            'address'   => $newest->address,
            'timestamp' => $newest->timestamp,
            'value'     => Carbon::createFromTimestamp((int) $newest->timestamp / 1000)->format(DateFormat::DATE),
        ]);

        // Cache the updated_at value of the newest wallet
        Cache::put($cacheKey, $newest->updated_at);

        return $newest;
    }
}
