<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Concerns\DispatchesStatisticsEvents;
use App\Events\Statistics\AddressHoldings;
use App\Events\Statistics\UniqueAddresses;
use App\Facades\Network;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Addresses\Aggregates\HoldingsAggregate;
use App\Services\Addresses\Aggregates\LatestWalletAggregate;
use App\Services\Cache\StatisticsCache;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

final class CacheAddressStatistics extends Command
{
    use DispatchesStatisticsEvents;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-address-statistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache expensive address statistics';

    public function handle(StatisticsCache $cache): void
    {
        $hasAddressHoldings = $this->cacheHoldings($cache);

        $this->cacheGenesis($cache);
        $this->cacheNewest();
        $this->cacheMostTransactions($cache);
        $this->cacheLargest($cache);

        if ($hasAddressHoldings) {
            AddressHoldings::dispatch();
        }

        $this->dispatchEvent(UniqueAddresses::class);
    }

    private function cacheHoldings(StatisticsCache $cache): bool
    {
        $holdings = (new HoldingsAggregate())->aggregate();

        $hasChanges = false;
        if ($holdings !== null) {
            if ($cache->getAddressHoldings() !== $holdings->toArray()) {
                $hasChanges = true;
            }

            $cache->setAddressHoldings($holdings->toArray());
        }

        return $hasChanges;
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

    private function cacheNewest(): void
    {
        $newest = (new LatestWalletAggregate())->aggregate();
        if ($newest === null) {
            return;
        }

        $this->hasChanges = true;
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
            if (! $this->hasChanges) {
                $currentValue = $cache->getMostTransactions() ?? [];
                if (Arr::get($currentValue, 'address') !== $mostTransactions['address']) {
                    $this->hasChanges = true;
                } elseif (Arr::get($currentValue, 'value') !== $mostTransactions['tx_count']) {
                    $this->hasChanges = true;
                }
            }

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
            if (! $this->hasChanges) {
                $currentValue = $cache->getLargestAddress() ?? [];
                if (Arr::get($currentValue, 'address') !== $largest->address) {
                    $this->hasChanges = true;
                } elseif (Arr::get($currentValue, 'value') !== $largest->balance->toFloat()) {
                    $this->hasChanges = true;
                }
            }

            $cache->setLargestAddress([
                'address' => $largest->address,
                'value'   => $largest->balance->toFloat(),
            ]);
        }
    }
}
