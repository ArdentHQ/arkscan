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
     * @var string|null
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
        $genesis = Transaction::orderBy('block_number', 'asc')->limit(1)->first();

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
        
        $mostActive = DB::connection('explorer')->query()
            ->fromSub(function ($query) {
                $query->select('from as address')
                      ->from('transactions')

                ->unionAll(
                    Transaction::select('to as address')
                               ->whereNotNull('to')
                )

                ->unionAll(
                    Transaction::selectRaw('unnest(multi_payment_recipients) as address')
                               ->whereNotNull('multi_payment_recipients')
                )

                ->unionAll(
                    Transaction::select('wallets.address')
                               ->join('wallets', 'transactions.sender_public_key', '=', 'wallets.public_key')
                               ->whereColumn('transactions.from', '!=', 'wallets.address')
                               ->orWhereNull('transactions.from')
                );
            }, 'activity')

            ->select('address')
            ->selectRaw('COUNT(*) as tx_count')
            ->whereNotNull('address')
            ->where('address', '!=', '')
            ->groupBy('address')
            ->orderByDesc('tx_count')
            ->limit(1)
            ->first();  
    
        /** @var object{address?: string, tx_count?: int} $mostActive */
        if ($mostActive?->address !== null) {
            $newValue = [
                'address' => $mostActive->address,
                'value'   => (int) $mostActive->tx_count,
            ];

            $current = $cache->getMostTransactions() ?? [];
            if ($current !== $newValue) {
                $this->hasChanges = true;
            }

            $cache->setMostTransactions($newValue);
        }
    }

    private function cacheLargest(StatisticsCache $cache): void
    {
        $largest = Wallet::orderBy('balance', 'desc')->limit(1)->first();

        if ($largest !== null) {
            /** @var Wallet $largest */
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
