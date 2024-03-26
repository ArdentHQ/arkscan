<?php

declare(strict_types=1);

namespace App\Http\Livewire\Stats;

use App\DTO\Statistics\AddressHoldingStatistics;
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
use App\DTO\Statistics\ValidatorStatistics;
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
    /** @var mixed */
    protected $listeners = ['currencyChanged' => '$refresh'];

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

            'validatorDetails'   => $this->validatorDetails($statisticsCache),
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
        $currency = Settings::currency();

        return MarketDataPriceStatistics::make(
            TimestampedValue::fromArray($cache->getPriceAtl($currency)),
            TimestampedValue::fromArray($cache->getPriceAth($currency)),
            LowHighValue::fromArray($cache->getPriceRangeDaily($currency)),
            LowHighValue::fromArray($cache->getPriceRange52($currency)),
        );
    }

    private function marketDataVolume(StatisticsCache $cache): MarketDataVolumeStatistics
    {
        $currency  = Settings::currency();

        return MarketDataVolumeStatistics::make(
            (new CryptoDataCache())->getVolume($currency),
            TimestampedValue::fromArray($cache->getVolumeAtl($currency)),
            TimestampedValue::fromArray($cache->getVolumeAth($currency)),
        );
    }

    private function marketDataCap(StatisticsCache $cache): MarketDataRecordStatistics
    {
        $currency = Settings::currency();

        return MarketDataRecordStatistics::make(
            MarketCap::get(Network::currency(), $currency),
            TimestampedValue::fromArray($cache->getMarketCapAtl($currency)),
            TimestampedValue::fromArray($cache->getMarketCapAth($currency)),
        );
    }

    private function validatorDetails(StatisticsCache $cache): ValidatorStatistics
    {
        $mostUniqueVoters  = Wallet::firstWhere('public_key', $cache->getMostUniqueVoters());
        $leastUniqueVoters = Wallet::firstWhere('public_key', $cache->getLeastUniqueVoters());
        $mostBlocksForged  = Wallet::firstWhere('public_key', $cache->getMostBlocksForged());

        $oldestActiveValidatorData = $cache->getOldestActiveValidator();
        if ($oldestActiveValidatorData !== null) {
            $oldestActiveValidator = Wallet::firstWhere('public_key', $oldestActiveValidatorData['publicKey']);
            if ($oldestActiveValidator !== null) {
                $oldestActiveValidator = WalletWithValue::make($oldestActiveValidator, Carbon::createFromTimestamp($oldestActiveValidatorData['timestamp']));
            }
        }

        $newestActiveValidatorData = $cache->getNewestActiveValidator();
        if ($newestActiveValidatorData !== null) {
            $newestActiveValidator = Wallet::firstWhere('public_key', $newestActiveValidatorData['publicKey']);
            if ($newestActiveValidator !== null) {
                $newestActiveValidator = WalletWithValue::make($newestActiveValidator, Carbon::createFromTimestamp($newestActiveValidatorData['timestamp']));
            }
        }

        return ValidatorStatistics::make(
            $mostUniqueVoters,
            $leastUniqueVoters,
            $mostBlocksForged,
            $oldestActiveValidator ?? null,
            $newestActiveValidator ?? null,
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
