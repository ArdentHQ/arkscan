<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\MarketDataProvider;
use App\Facades\Network;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\StatisticsCache;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

final class CacheMarketDataStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-market-data-statistics';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Cache expensive market data statistics';

    public function handle(MarketDataProvider $marketDataProvider, StatisticsCache $cache, CryptoDataCache $crypto): void
    {
        if (! Network::canBeExchanged()) {
            return;
        }

        /** @var array<string, array<string, string>> */
        $allCurrencies = config('currencies');

        $currencies = collect($allCurrencies)->pluck('currency');

        $currencies->each(function ($currency) use ($cache, $crypto): void {
            // Grab prices from cache based on last cached value from CachePrices command
            $allTimeData = $crypto->getHistoricalFullResponse(Network::currency(), $currency);
            $dailyData   = $crypto->getHistoricalHourlyFullResponse(Network::currency(), $currency);

            if (count($allTimeData) === 0 || count($dailyData) === 0) {
                return;
            }

            $this->cachePriceStats($currency, $allTimeData, $dailyData, $cache);
            $this->cacheVolumeStats($currency, $allTimeData, $cache);
            $this->cacheMarketCapStats($currency, $allTimeData, $cache);
        });
    }

    /**
     * @param array{prices: array{0:int, 1:float}[], market_caps: array{0:int, 1:float}[], total_volumes: array{0:int, 1:float}[]} $allTimeData
     * @param array{prices: array{0:int, 1:float}[], market_caps: array{0:int, 1:float}[], total_volumes: array{0:int, 1:float}[]} $dailyData
     */
    private function cachePriceStats(string $currency, array $allTimeData, array $dailyData, StatisticsCache $cache): void
    {
        $prices = collect($allTimeData['prices'])
            ->map(fn ($item) => ['timestamp' => $item[0], 'value' => $item[1]]);

        $pricesSorted = $prices->sortBy('value');

        /** @var array{timestamp: int, value: float|null} $priceAtl */
        $priceAtl = $pricesSorted->first();
        /** @var array{timestamp: int, value: float|null} $priceAth */
        $priceAth = $pricesSorted->last();

        if ($priceAtl['value'] !== null) {
            $cache->setPriceAtl($currency, $priceAtl['timestamp'] / 1000, $priceAtl['value']);
        }

        if ($priceAth['value'] !== null) {
            $cache->setPriceAth($currency, $priceAth['timestamp'] / 1000, $priceAth['value']);
        }

        $this->cache52WeekPriceStats($currency, $prices, $cache);
        $this->cacheDailyPriceStats($currency, $dailyData, $cache);
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

        /** @var array{timestamp: int, value: float|null} $volumeAtl */
        $volumeAtl = $volumeSorted->first();
        /** @var array{timestamp: int, value: float|null} $volumeAth */
        $volumeAth = $volumeSorted->last();

        if ($volumeAtl['value'] !== null) {
            $cache->setVolumeAtl($currency, $volumeAtl['timestamp'] / 1000, $volumeAtl['value']);
        }

        if ($volumeAth['value'] !== null) {
            $cache->setVolumeAth($currency, $volumeAth['timestamp'] / 1000, $volumeAth['value']);
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

        /** @var array{timestamp: int, value: float|null} $marketCapAtl */
        $marketCapAtl = $marketCapSorted->first();
        /** @var array{timestamp: int, value: float|null} $marketCapAth */
        $marketCapAth = $marketCapSorted->last();

        if ($marketCapAtl['value'] !== null) {
            $cache->setMarketCapAtl($currency, $marketCapAtl['timestamp'] / 1000, $marketCapAtl['value']);
        }

        if ($marketCapAth['value'] !== null) {
            $cache->setMarketCapAth($currency, $marketCapAth['timestamp'] / 1000, $marketCapAth['value']);
        }
    }
}
