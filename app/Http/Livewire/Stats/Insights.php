<?php

declare(strict_types=1);

namespace App\Http\Livewire\Stats;

use App\DTO\Statistics\AddressHoldingStatistics;
use App\DTO\Statistics\DelegateStatistics;
use App\DTO\Statistics\LowHighValue;
use App\DTO\Statistics\MarketDataPriceStatistics;
use App\DTO\Statistics\MarketDataRecordStatistics;
use App\DTO\Statistics\MarketDataStatistics;
use App\DTO\Statistics\MarketDataVolumeStatistics;
use App\DTO\Statistics\TimestampedValue;
use App\DTO\Statistics\TransactionAveragesStatistics;
use App\DTO\Statistics\TransactionRecordsStatistics;
use App\DTO\Statistics\TransactionStatistics;
use App\DTO\Statistics\UniqueAddressesStatistics;
use App\DTO\Statistics\WalletWithValue;
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
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Component;

final class Insights extends Component
{
    public TransactionStatistics $transactionDetails;

    public MarketDataStatistics $marketData;

    public DelegateStatistics $delegateDetails;

    public AddressHoldingStatistics $addressHoldings;

    public UniqueAddressesStatistics $uniqueAddresses;

    public array $annualData;

    /** @var mixed */
    protected $listeners = [
        // 'currencyChanged' => 'updateData',
        'echo:statistics-update.transaction-details,StatisticsUpdate' => 'updateTransactionDetails',
        'echo:statistics-update.market-data,StatisticsUpdate' => 'updateMarketData',
        'echo:statistics-update.delegate-details,StatisticsUpdate' => 'updateDelegateDetails',
        'echo:statistics-update.address-holdings,StatisticsUpdate' => 'updateAddressHoldings',
        'echo:statistics-update.unique-addresses,StatisticsUpdate' => 'updateUniqueAddresses',
        'echo:statistics-update.annual-data,StatisticsUpdate' => 'updateAnnualData',
    ];

    public function mount(): void
    {
        $this->updateData();
    }

    public function updateData(): void
    {
        $this->updateTransactionDetails();
        $this->updateMarketData();
        $this->updateDelegateDetails();
        $this->updateAddressHoldings();
        $this->updateUniqueAddresses();
        $this->updateAnnualData();
    }

    public function updateTransactionDetails(): void
    {
        $transactionCache = new TransactionCache();

        $this->transactionDetails = TransactionStatistics::make(
            $this->getTransactionDetails($transactionCache),
            $this->getTransactionAverages($transactionCache),
            $this->getTransactionRecords($transactionCache),
        );
    }

    public function updateMarketData(): void
    {
        $statisticsCache = new StatisticsCache();

        $this->marketData = MarketDataStatistics::make(
            $this->getMarketDataPrice($statisticsCache),
            $this->getMarketDataVolume($statisticsCache),
            $this->getMarketDataCap($statisticsCache),
        );
    }

    public function updateDelegateDetails(): void
    {
        $statisticsCache = new StatisticsCache();

        $this->delegateDetails = $this->getDelegateDetails($statisticsCache);
    }

    public function updateAddressHoldings(): void
    {
        $statisticsCache = new StatisticsCache();

        $this->addressHoldings = $this->getAddressHoldings($statisticsCache);
    }

    public function updateUniqueAddresses(): void
    {
        $statisticsCache = new StatisticsCache();

        $this->uniqueAddresses = $this->getUniqueAddresses($statisticsCache);
    }

    public function updateAnnualData(): void
    {
        $statisticsCache = new StatisticsCache();

        $this->annualData = $this->getAnnualData($statisticsCache);
    }

    public function render(): View
    {
        return view('livewire.stats.insights');
    }

    private function getTransactionDetails(TransactionCache $cache): array
    {
        return StatsTransactionType::all()
            ->mapWithKeys(fn ($type) => [$type => $cache->getHistoricalByType($type)])
            ->toArray();
    }

    private function getTransactionAverages(TransactionCache $cache): TransactionAveragesStatistics
    {
        return TransactionAveragesStatistics::make($cache->getHistoricalAverages());
    }

    private function getTransactionRecords(TransactionCache $transactionCache): TransactionRecordsStatistics
    {
        $blockCache = new BlockCache();

        return TransactionRecordsStatistics::make(
            Transaction::find($transactionCache->getLargestIdByAmount()),
            Block::find($blockCache->getLargestIdByAmount()),
            Block::find($blockCache->getLargestIdByFees()),
            Block::find($blockCache->getLargestIdByTransactionCount()),
        );
    }

    private function getMarketDataPrice(StatisticsCache $cache): MarketDataPriceStatistics
    {
        $currency = Settings::currency();

        return MarketDataPriceStatistics::make(
            TimestampedValue::fromArray($cache->getPriceAtl($currency)),
            TimestampedValue::fromArray($cache->getPriceAth($currency)),
            LowHighValue::fromArray($cache->getPriceRangeDaily($currency)),
            LowHighValue::fromArray($cache->getPriceRange52($currency)),
        );
    }

    private function getMarketDataVolume(StatisticsCache $cache): MarketDataVolumeStatistics
    {
        $currency  = Settings::currency();

        return MarketDataVolumeStatistics::make(
            (new CryptoDataCache())->getVolume($currency),
            TimestampedValue::fromArray($cache->getVolumeAtl($currency)),
            TimestampedValue::fromArray($cache->getVolumeAth($currency)),
        );
    }

    private function getMarketDataCap(StatisticsCache $cache): MarketDataRecordStatistics
    {
        $currency = Settings::currency();

        return MarketDataRecordStatistics::make(
            MarketCap::get(Network::currency(), $currency),
            TimestampedValue::fromArray($cache->getMarketCapAtl($currency)),
            TimestampedValue::fromArray($cache->getMarketCapAth($currency)),
        );
    }

    private function getDelegateDetails(StatisticsCache $cache): DelegateStatistics
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

    private function getAddressHoldings(StatisticsCache $cache): AddressHoldingStatistics
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

    private function getUniqueAddresses(StatisticsCache $cache): UniqueAddressesStatistics
    {
        return UniqueAddressesStatistics::make(
            $cache->getGenesisAddress(),
            $cache->getNewestAddress(),
            $cache->getMostTransactions(),
            $cache->getLargestAddress(),
        );
    }

    private function getAnnualData(StatisticsCache $cache): array
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
