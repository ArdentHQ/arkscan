<?php

declare(strict_types=1);

namespace App\Http\Livewire\Stats;

use App\Enums\StatsTransactionType;
use App\Facades\Network;
use App\Facades\Settings;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\BlockCache;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\StatisticsCache;
use App\Services\Cache\TransactionCache;
use App\Services\MarketCap;
use App\Services\NumberFormatter;
use App\ViewModels\BlockViewModel;
use App\ViewModels\TransactionViewModel;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
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
            'transactionDetails'  => $this->transactionDetails($transactionCache),
            'transactionAverages' => $this->transactionAverages($transactionCache),
            'transactionRecords'  => $this->transactionRecords($transactionCache),
            'marketDataPrice'     => $this->marketDataPrice($statisticsCache),
            'marketDataVolume'    => $this->marketDataVolume($statisticsCache),
            'marketDataCap'       => $this->marketDataCap($statisticsCache),
            'delegateDetails'     => $this->delegateDetails($statisticsCache),
            'addressHoldings'     => $this->addressHoldings($statisticsCache),
            'uniqueAddresses'     => $this->uniqueAddresses($statisticsCache),
            'annualData'          => $this->annualData($statisticsCache),
        ]);
    }

    private function transactionDetails(TransactionCache $cache): array
    {
        return StatsTransactionType::all()
            ->mapWithKeys(fn ($type) => [$type => $cache->getHistoricalByType($type)])
            ->toArray();
    }

    private function transactionAverages(TransactionCache $cache): array
    {
        $data = $cache->getHistoricalAverages();

        return [
            'transactions'       => $data['count'],
            'transaction_volume' => NumberFormatter::currency($data['amount'], Network::currency()),
            'transaction_fees'   => NumberFormatter::currency($data['fee'], Network::currency()),
        ];
    }

    private function transactionRecords(TransactionCache $transactionCache): array
    {
        $blockCache = new BlockCache();

        $largestTransaction = Transaction::find($transactionCache->getLargestIdByAmount());
        if ($largestTransaction !== null) {
            $largestTransaction = new TransactionViewModel($largestTransaction);
        }

        $largestBlock = Block::find($blockCache->getLargestIdByAmount());
        if ($largestBlock !== null) {
            $largestBlock = new BlockViewModel($largestBlock);
        }

        $blockWithHighestFees = Block::find($blockCache->getLargestIdByFees());
        if ($blockWithHighestFees !== null) {
            $blockWithHighestFees = new BlockViewModel($blockWithHighestFees);
        }

        $blockWithMostTransactions = Block::find($blockCache->getLargestIdByTransactionCount());
        if ($blockWithMostTransactions !== null) {
            $blockWithMostTransactions = new BlockViewModel($blockWithMostTransactions);
        }

        return [
            'largest_transaction'        => $largestTransaction,
            'largest_block'              => $largestBlock,
            'highest_fee'                => $blockWithHighestFees,
            'most_transactions_in_block' => $blockWithMostTransactions,
        ];
    }

    private function marketDataPrice(StatisticsCache $cache): array
    {
        $priceAtl        = $cache->getPriceAtl();
        $priceAth        = $cache->getPriceAth();
        $priceRangeDaily = $cache->getPriceRangeDaily();
        $priceRange52w   = $cache->getPriceRange52();

        return [
            'daily_low'  => $priceRangeDaily !== null ? $priceRangeDaily['low'] : 0,
            'daily_high' => $priceRangeDaily !== null ? $priceRangeDaily['high'] : 0,
            '52w_low'    => $priceRange52w !== null ? $priceRange52w['low'] : 0,
            '52w_high'   => $priceRange52w !== null ? $priceRange52w['high'] : 0,
            'atl'        => $priceAtl !== null ? $priceAtl['value'] : 0,
            'atl_date'   => $priceAtl !== null ? $priceAtl['timestamp'] : null,
            'ath'        => $priceAth !== null ? $priceAth['value'] : 0,
            'ath_date'   => $priceAth !== null ? $priceAth['timestamp'] : null,
        ];
    }

    private function marketDataVolume(StatisticsCache $cache): array
    {
        $volume    = (new CryptoDataCache())->getVolume('USD');
        $volumeAtl = $cache->getVolumeAtl();
        $volumeAth = $cache->getVolumeAth();

        return [
            'today_volume' => $volume ?? 0,
            'atl'          => $volumeAtl !== null ? $volumeAtl['value'] : 0,
            'atl_date'     => $volumeAtl !== null ? $volumeAtl['timestamp'] : null,
            'ath'          => $volumeAth !== null ? $volumeAth['value'] : 0,
            'ath_date'     => $volumeAth !== null ? $volumeAth['timestamp'] : null,
        ];
    }

    private function marketDataCap(StatisticsCache $cache): array
    {
        $marketCapAtl = $cache->getMarketCapAtl();
        $marketCapAth = $cache->getMarketCapAth();

        return [
            'today_value' => MarketCap::get(Network::currency(), 'USD'),
            'atl'         => $marketCapAtl !== null ? $marketCapAtl['value'] : 0,
            'atl_date'    => $marketCapAtl !== null ? $marketCapAtl['timestamp'] : null,
            'ath'         => $marketCapAth !== null ? $marketCapAth['value'] : 0,
            'ath_date'    => $marketCapAth !== null ? $marketCapAth['timestamp'] : null,
        ];
    }

    private function delegateDetails(StatisticsCache $cache): array
    {
        $mostUniqueVoters = Wallet::firstWhere('public_key', $cache->getMostUniqueVoters());
        if ($mostUniqueVoters !== null) {
            $mostUniqueVoters = ViewModelFactory::make($mostUniqueVoters);
        }

        $leastUniqueVoters = Wallet::firstWhere('public_key', $cache->getLeastUniqueVoters());
        if ($leastUniqueVoters !== null) {
            $leastUniqueVoters = ViewModelFactory::make($leastUniqueVoters);
        }

        $oldestActiveDelegateData = $cache->getOldestActiveDelegate();
        if ($oldestActiveDelegateData !== null) {
            $oldestActiveDelegate = Wallet::firstWhere('public_key', $oldestActiveDelegateData['publicKey']);
            if ($oldestActiveDelegate !== null) {
                $oldestActiveDelegate = [
                    'delegate' => ViewModelFactory::make($oldestActiveDelegate),
                    'value'    => Carbon::createFromTimestamp($oldestActiveDelegateData['timestamp'])->format(DateFormat::DATE),
                ];
            }
        }

        $newestActiveDelegateData = $cache->getNewestActiveDelegate();
        if ($newestActiveDelegateData !== null) {
            $newestActiveDelegate = Wallet::firstWhere('public_key', $newestActiveDelegateData['publicKey']);
            if ($newestActiveDelegate !== null) {
                $newestActiveDelegate = [
                    'delegate' => ViewModelFactory::make($newestActiveDelegate),
                    'value'    => Carbon::createFromTimestamp($newestActiveDelegateData['timestamp'])->format(DateFormat::DATE),
                ];
            }
        }

        $mostBlocksForged = Wallet::firstWhere('public_key', $cache->getMostBlocksForged());
        if ($mostBlocksForged !== null) {
            $mostBlocksForged = ViewModelFactory::make($mostBlocksForged);
        }

        return [
            'most_unique_voters'     => $mostUniqueVoters,
            'least_unique_voters'    => $leastUniqueVoters,
            'oldest_active_delegate' => $oldestActiveDelegate ?? null,
            'newest_active_delegate' => $newestActiveDelegate ?? null,
            'most_blocks_forged'     => $mostBlocksForged,
        ];
    }

    private function addressHoldings(StatisticsCache $cache): array
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

        return $summedValues;
    }

    private function uniqueAddresses(StatisticsCache $cache): array
    {
        $genesis          = $cache->getGenesisAddress();
        $newest           = $cache->getNewestAddress();
        $mostTransactions = $cache->getMostTransactions();
        $largest          = $cache->getLargestAddress();

        return [
            'genesis'           => $genesis,
            'newest'            => $newest,
            'most_transactions' => $mostTransactions,
            'largest'           => $largest,
        ];
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
