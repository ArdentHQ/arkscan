<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Concerns\DispatchesStatisticsEvents;
use App\Contracts\MarketDataProvider;
use App\Events\Statistics\MarketData;
use App\Facades\Network;
use App\Services\Cache\StatisticsCache;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class CacheMarketDataStatistics extends Command
{
    use DispatchesStatisticsEvents;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-market-data-statistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache expensive market data statistics';

    public function handle(MarketDataProvider $marketDataProvider, StatisticsCache $cache): void
    {
        if (! Network::canBeExchanged()) {
            return;
        }

        /** @var array<string, array<string, string>> */
        $allCurrencies = config('currencies');

        $currencies = collect($allCurrencies)->pluck('currency');

        $currencies->each(function ($currency) use ($cache, $marketDataProvider): void {
            // Grab series based on last cached value from CachePrices command
            $allTimeData = $marketDataProvider->marketChart(Network::currency(), $currency);
            $dailyData   = $marketDataProvider->marketChartHourly(Network::currency(), $currency);

            if (count($allTimeData) === 0 || count($dailyData) === 0) {
                return;
            }

            $this->cachePriceStats($currency, $allTimeData, $dailyData, $cache);
            $this->cacheVolumeStats($currency, $allTimeData, $cache);
            $this->cacheMarketCapStats($currency, $allTimeData, $cache);
        });

        $this->cacheAllTimePrices($currencies, $cache, $marketDataProvider);

        $this->dispatchEvent(MarketData::class);
    }

    /**
     * @param array{prices: array{0:int, 1:float}[], market_caps: array{0:int, 1:float}[], total_volumes: array{0:int, 1:float}[]} $allTimeData
     * @param array{prices: array{0:int, 1:float}[], market_caps: array{0:int, 1:float}[], total_volumes: array{0:int, 1:float}[]} $dailyData
     */
    private function cachePriceStats(string $currency, array $allTimeData, array $dailyData, StatisticsCache $cache): void
    {
        $prices = collect($allTimeData['prices'])
            ->map(fn ($item) => ['timestamp' => $item[0], 'value' => $item[1]]);

        $this->cache52WeekPriceStats($currency, $prices, $cache);
        $this->cacheDailyPriceStats($currency, $dailyData, $cache);
    }

    private function cacheAllTimePrices(Collection $currencies, StatisticsCache $statisticsCache, MarketDataProvider $marketDataProvider): void
    {
        $highLows = $marketDataProvider->allTimeHighLow(Network::currency(), $currencies);

        foreach ($currencies as $currency) {
            /** @var array{ath: array{value: float, timestamp: int}|null, atl: array{value: float, timestamp: int}|null}|null $highLow */
            $highLow = $highLows->get(strtoupper($currency));

            if ($highLow === null) {
                continue;
            }

            $priceAtl = $highLow['atl'];
            if ($priceAtl !== null) {
                $existingValue = $statisticsCache->getPriceAtl($currency) ?? [];
                if (Arr::get($existingValue, 'timestamp') !== $priceAtl['timestamp']) {
                    $this->hasChanges = true;
                } elseif (Arr::get($existingValue, 'value') !== $priceAtl['value']) {
                    $this->hasChanges = true;
                }

                $statisticsCache->setPriceAtl($currency, $priceAtl['timestamp'], $priceAtl['value']);
            }

            $priceAth = $highLow['ath'];
            if ($priceAth !== null) {
                $existingValue = $statisticsCache->getPriceAth($currency) ?? [];
                if (Arr::get($existingValue, 'timestamp') !== $priceAth['timestamp']) {
                    $this->hasChanges = true;
                } elseif (Arr::get($existingValue, 'value') !== $priceAth['value']) {
                    $this->hasChanges = true;
                }

                $statisticsCache->setPriceAth($currency, $priceAth['timestamp'], $priceAth['value']);
            }
        }
    }

    private function cache52WeekPriceStats(string $currency, Collection $data, StatisticsCache $cache): void
    {
        $start52WeeksAgo = (int) Carbon::now()->subWeeks(52)->timestamp * 1000;
        $prices52Week    = $data->filter(function ($item) use ($start52WeeksAgo) {
            return $item['timestamp'] > $start52WeeksAgo;
        });

        $pricesSorted = $prices52Week->sortBy('value');

        /** @var array{timestamp: int, value: float} $priceLow52 */
        $priceLow52  = $pricesSorted->first();
        /** @var array{timestamp: int, value: float} $priceHigh52 */
        $priceHigh52 = $pricesSorted->last();

        if (! $this->hasChanges) {
            $existingValue = $cache->getPriceRange52($currency) ?? [];
            if (Arr::get($existingValue, 'low') !== $priceLow52['value']) {
                $this->hasChanges = true;
            } elseif (Arr::get($existingValue, 'high') !== $priceHigh52['value']) {
                $this->hasChanges = true;
            }
        }

        $cache->setPriceRange52($currency, $priceLow52['value'], $priceHigh52['value']);
    }

    /**
     * @param array{prices: array{0:int, 1:float}[], market_caps: array{0:int, 1:float}[], total_volumes: array{0:int, 1:float}[]} $data
     */
    private function cacheDailyPriceStats(string $currency, array $data, StatisticsCache $cache): void
    {
        $prices = collect($data['prices'])
            ->map(fn ($item) => ['timestamp' => $item[0], 'value' => $item[1]]);

        $priceSorted = $prices->sortBy('value');

        /** @var array{timestamp: int, value: float} $priceDailyLow */
        $priceDailyLow  = $priceSorted->first();
        /** @var array{timestamp: int, value: float} $priceDailyHigh */
        $priceDailyHigh = $priceSorted->last();

        if (! $this->hasChanges) {
            $existingValue = $cache->getPriceRangeDaily($currency) ?? [];
            if (Arr::get($existingValue, 'low') !== $priceDailyLow['value']) {
                $this->hasChanges = true;
            } elseif (Arr::get($existingValue, 'high') !== $priceDailyHigh['value']) {
                $this->hasChanges = true;
            }
        }

        $cache->setPriceRangeDaily($currency, $priceDailyLow['value'], $priceDailyHigh['value']);
    }

    /**
     * @param array{prices: array{0:int, 1:float}[], market_caps: array{0:int, 1:float}[], total_volumes: array{0:int, 1:float}[]} $data
     */
    private function cacheVolumeStats(string $currency, array $data, StatisticsCache $cache): void
    {
        $volumes = collect($data['total_volumes'])
            ->map(fn ($item) => ['timestamp' => $item[0], 'value' => $item[1]]);

        $volumeSorted = $volumes->sortBy('value');

        /** @var array{timestamp: int, value: int|float|null} $volumeAtl */
        $volumeAtl = $volumeSorted->first();
        /** @var array{timestamp: int, value: int|float|null} $volumeAth */
        $volumeAth = $volumeSorted->last();

        if ($volumeAtl['value'] !== null) {
            $existingValue = Arr::get($cache->getVolumeAtl($currency) ?? [], 'value');
            if ($existingValue === null || (float) $volumeAtl['value'] < (float) $existingValue) {
                $this->hasChanges = true;

                $cache->setVolumeAtl($currency, $volumeAtl['timestamp'] / 1000, $volumeAtl['value']);
            }
        }

        if ($volumeAth['value'] !== null) {
            $existingValue = Arr::get($cache->getVolumeAth($currency) ?? [], 'value');
            if ($existingValue === null || (float) $volumeAth['value'] > (float) $existingValue) {
                $this->hasChanges = true;

                $cache->setVolumeAth($currency, $volumeAth['timestamp'] / 1000, $volumeAth['value']);
            }
        }
    }

    /**
     * @param array{prices: array{0:int, 1:float}[], market_caps: array{0:int, 1:float}[], total_volumes: array{0:int, 1:float}[]} $data
     */
    private function cacheMarketCapStats(string $currency, array $data, StatisticsCache $cache): void
    {
        $marketcaps = collect($data['market_caps'])
            ->map(fn ($item) => ['timestamp' => $item[0], 'value' => $item[1]]);

        $marketCapSorted = $marketcaps->sortBy('value');

        /** @var array{timestamp: int, value: int|float|null} $marketCapAtl */
        $marketCapAtl = $marketCapSorted->first();
        /** @var array{timestamp: int, value: int|float|null} $marketCapAth */
        $marketCapAth = $marketCapSorted->last();

        if ($marketCapAtl['value'] !== null) {
            $existingValue = Arr::get($cache->getMarketCapAtl($currency) ?? [], 'value');
            if ($existingValue === null || (float) $marketCapAtl['value'] < (float) $existingValue) {
                $this->hasChanges = true;

                $cache->setMarketCapAtl($currency, $marketCapAtl['timestamp'] / 1000, $marketCapAtl['value']);
            }
        }

        if ($marketCapAth['value'] !== null) {
            $existingValue = Arr::get($cache->getMarketCapAth($currency) ?? [], 'value');
            if ($existingValue === null || (float) $marketCapAth['value'] > (float) $existingValue) {
                $this->hasChanges = true;

                $cache->setMarketCapAth($currency, $marketCapAth['timestamp'] / 1000, $marketCapAth['value']);
            }
        }
    }
}
