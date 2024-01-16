<?php

declare(strict_types=1);

namespace App\Http\Livewire\Stats;

use App\DTO\Statistics\AddressHoldingStatistics;
use App\DTO\Statistics\DelegateStatistics;
use App\DTO\Statistics\MarketDataPriceStatistics;
use App\DTO\Statistics\MarketDataRecordStatistics;
use App\DTO\Statistics\MarketDataStatistics;
use App\DTO\Statistics\MarketDataVolumeStatistics;
use App\DTO\Statistics\TransactionAveragesStatistics;
use App\DTO\Statistics\TransactionRecordsStatistics;
use App\DTO\Statistics\TransactionStatistics;
use App\DTO\Statistics\UniqueAddressesStatistics;
use App\DTO\Statistics\WalletWithValue;
use App\Enums\StatsTransactionType;
use App\Facades\Network;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\BlockCache;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\StatisticsCache;
use App\Services\Cache\TransactionCache;
use App\Services\MarketCap;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Component;

final class Insights extends Component
{
    public function render(): View
    {
        $transactionCache = new TransactionCache();
        $statisticsCache  = new StatisticsCache();

        return view('livewire.stats.insights', [
            'transactionDetails' => TransactionStatistics::make(
                $this->transactionDetails($transactionCache),
                $this->transactionAverages($transactionCache),
                $this->transactionRecords($transactionCache),
            ),

            'marketData' => MarketDataStatistics::make(
                $this->marketDataPrice($statisticsCache),
                $this->marketDataVolume($statisticsCache),
                $this->marketDataCap($statisticsCache),
            ),

            'delegateDetails'    => $this->delegateDetails($statisticsCache),
            'addressHoldings'    => $this->addressHoldings($statisticsCache),
            'uniqueAddresses'    => $this->uniqueAddresses($statisticsCache),
            'annualData'         => $this->annualData($statisticsCache),
        ]);
    }

    private function transactionDetails(TransactionCache $cache): array
    {
        return StatsTransactionType::all()
            ->mapWithKeys(fn ($type) => [$type => $cache->getHistoricalByType($type)])
            ->toArray();
    }

    private function transactionAverages(TransactionCache $cache): TransactionAveragesStatistics
    {
        return TransactionAveragesStatistics::make($cache->getHistoricalAverages());
    }

    private function transactionRecords(TransactionCache $transactionCache): TransactionRecordsStatistics
    {
        $blockCache = new BlockCache();

        return TransactionRecordsStatistics::make(
            Transaction::find($transactionCache->getLargestIdByAmount()),
            Block::find($blockCache->getLargestIdByAmount()),
            Block::find($blockCache->getLargestIdByFees()),
            Block::find($blockCache->getLargestIdByTransactionCount()),
        );
    }

    private function marketDataPrice(StatisticsCache $cache): MarketDataPriceStatistics
    {
        return MarketDataPriceStatistics::make(
            $cache->getPriceAtl(),
            $cache->getPriceAth(),
            $cache->getPriceRangeDaily(),
            $cache->getPriceRange52(),
        );
    }

    private function marketDataVolume(StatisticsCache $cache): MarketDataVolumeStatistics
    {
        return MarketDataVolumeStatistics::make(
            (new CryptoDataCache())->getVolume('USD'),
            $cache->getVolumeAtl(),
            $cache->getVolumeAth(),
        );
    }

    private function marketDataCap(StatisticsCache $cache): MarketDataRecordStatistics
    {
        return MarketDataRecordStatistics::make(
            MarketCap::get(Network::currency(), 'USD'),
            $cache->getMarketCapAtl(),
            $cache->getMarketCapAth(),
        );
    }

    private function delegateDetails(StatisticsCache $cache): DelegateStatistics
    {
        $mostUniqueVoters  = Wallet::firstWhere('public_key', $cache->getMostUniqueVoters());
        $leastUniqueVoters = Wallet::firstWhere('public_key', $cache->getLeastUniqueVoters());
        $mostBlocksForged  = Wallet::firstWhere('public_key', $cache->getMostBlocksForged());

        $oldestActiveDelegateData = $cache->getOldestActiveDelegate();
        if ($oldestActiveDelegateData !== null) {
            $oldestActiveDelegate = Wallet::firstWhere('public_key', $oldestActiveDelegateData['publicKey']);
            if ($oldestActiveDelegate !== null) {
                $oldestActiveDelegate = WalletWithValue::make($oldestActiveDelegate, Carbon::createFromTimestamp($oldestActiveDelegateData['timestamp']));
            }
        }

        $newestActiveDelegateData = $cache->getNewestActiveDelegate();
        if ($newestActiveDelegateData !== null) {
            $newestActiveDelegate = Wallet::firstWhere('public_key', $newestActiveDelegateData['publicKey']);
            if ($newestActiveDelegate !== null) {
                $newestActiveDelegate = WalletWithValue::make($newestActiveDelegate, Carbon::createFromTimestamp($newestActiveDelegateData['timestamp']));
            }
        }

        return DelegateStatistics::make(
            $mostUniqueVoters,
            $leastUniqueVoters,
            $mostBlocksForged,
            $oldestActiveDelegate ?? null,
            $newestActiveDelegate ?? null,
        );
    }

    private function addressHoldings(StatisticsCache $cache): AddressHoldingStatistics
    {
        $holdings = $cache->getAddressHoldings();

        unset($holdings['0']); // Ignore wallets below 1

        // Create new array with summed values instead of pure counts
        $previousValue = 0;
        $summedValues  = [];
        foreach (array_reverse($holdings) as $key => $values) {
            array_unshift($summedValues, ['grouped' => $values['grouped'], 'count' => $values['count'] + $previousValue]);
            $previousValue = $values['count'] + $previousValue;
        }

        return AddressHoldingStatistics::make($summedValues);
    }

    private function uniqueAddresses(StatisticsCache $cache): UniqueAddressesStatistics
    {
        return UniqueAddressesStatistics::make(
            $cache->getGenesisAddress(),
            $cache->getNewestAddress(),
            $cache->getMostTransactions(),
            $cache->getLargestAddress(),
        );
    }

    private function annualData(StatisticsCache $cache): array
    {
        $startYear   = Carbon::parse(Network::epoch())->year;
        $currentYear = Carbon::now()->year;
        $yearData    = [];

        for ($year = $startYear; $year <= $currentYear; $year++) {
            $yearData[] = $cache->getAnnualData($year);
        }

        return array_filter($yearData, fn ($item) => $item !== null);
    }
}
