<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Network;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Addresses\Aggregates\HoldingsAggregate;
use App\Services\Cache\StatisticsCache;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class CacheAddressStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-address-statistics';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Cache expensive address statistics';

    public function handle(StatisticsCache $cache): void
    {
        $this->cacheHoldings($cache);
        $this->cacheGenesis($cache);
        $this->cacheNewest($cache);
        $this->cacheMostTransactions($cache);
        $this->cacheLargest($cache);
    }

    private function cacheHoldings(StatisticsCache $cache): void
    {
        $holdings = (new HoldingsAggregate())->aggregate();

        if ($holdings !== null) {
            $cache->setAddressHoldings($holdings->toArray());
        }
    }

    private function cacheGenesis(StatisticsCache $cache): void
    {
        $genesis = Transaction::orderBy('block_height', 'asc')->limit(1)->first();
        if ($genesis !== null) {
            $cache->setGenesisAddress([
                'address' => $genesis->sender->address,
                'value'   => Carbon::createFromTimestamp(Network::epoch()->timestamp)->format(DateFormat::DATE),
            ]);
        }
    }

    private function cacheNewest(StatisticsCache $cache): void
    {
        $cacheKey = 'commands:cache_address_statistics/last_run';

        $newestQuery = Wallet::query()
            ->select('wallets.address', DB::raw('MIN(transactions.timestamp) as timestamp'))
            ->leftJoin('transactions', function ($join) {
                $join->on('wallets.public_key', '=', 'transactions.sender_public_key')
                    ->orOn('wallets.address', '=', 'transactions.recipient_id');
            })
            ->whereNotNull('transactions.sender_public_key')
            ->whereNotNull('transactions.recipient_id')
            ->groupBy('wallets.address')
            ->orderBy('timestamp', 'desc')
            ->limit(1);

        $lastRun = Cache::get($cacheKey, null);

        if ($lastRun !== null) {
            // FIXME
            // $newestQuery->where('wallets.updated_at', '>', $lastRun);
        }

        $newest = $newestQuery->first();

        if ($newest !== null) {
            $currentAddress = $cache->getNewestAddress();
            // Only store if later wallet is actually newer than previously cached wallet
            if ($currentAddress === null || $currentAddress['timestamp'] < $newest->timestamp) {
                $cache->setNewestAddress([
                    'address'   => $newest->address,
                    'timestamp' => $newest->timestamp,
                    'value'     => Carbon::createFromTimestamp($newest->timestamp / 1000)->format(DateFormat::DATE),
                ]);
            }
        }

        // Cache last run timestamp for newest wallet query
        Cache::rememberForever($cacheKey, function () {
            return Carbon::now();
        });
    }

    private function cacheMostTransactions(StatisticsCache $cache): void
    {
        /** @var array{address?: string, tx_count?: int} $mostTransactions */
        $mostTransactions = (array) DB::connection('explorer')
            ->query()
            ->select([
                DB::raw('count(transactions.id) as tx_count'),
                DB::raw('wallets.address'),
            ])
            ->from('transactions')
            ->join('wallets', 'transactions.sender_public_key', '=', 'wallets.public_key')
            ->groupBy('wallets.address')
            ->orderBy('tx_count', 'desc')
            ->limit(1)
            ->first();

        if (count($mostTransactions) > 0) {
            $cache->setMostTransactions([
                'address' => $mostTransactions['address'],
                'value'   => $mostTransactions['tx_count'],
            ]);
        }
    }

    private function cacheLargest(StatisticsCache $cache): void
    {
        $largest = Wallet::orderBy('balance', 'desc')->limit(1)->first();
        if ($largest !== null) {
            $cache->setLargestAddress([
                'address' => $largest->address,
                'value'   => $largest->balance->toFloat(),
            ]);
        }
    }
}
