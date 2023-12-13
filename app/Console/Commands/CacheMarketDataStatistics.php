<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\MarketDataProvider;
use App\Facades\Network;
use App\Services\Cache\StatisticsCache;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

/**
 * @phpstan-type MarketDataArray array{prices: array{0:int, 1:float}[], market_caps: array{0:int, 1:float}[], total_volumes: array{0:int, 1:float}[]}
 */
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

    public function handle(MarketDataProvider $marketDataProvider, StatisticsCache $cache): void
    {
        $allTimeData = $marketDataProvider->historicalAll(Network::currency(), 'usd', Network::epoch()->diffInDays());
        $dailyData   = $marketDataProvider->historicalAll(Network::currency(), 'usd');

        if (count($allTimeData) === 0 || count($dailyData) === 0) {
            return;
        }

        $this->cachePriceStats($allTimeData, $dailyData, $cache);
        $this->cacheVolumeStats($allTimeData, $cache);
        $this->cacheMarketCapStats($allTimeData, $cache);
    }

    /**
     * @param MarketDataArray $allTimeData
     * @param MarketDataArray $dailyData
     */
    private function cachePriceStats(array $allTimeData, array $dailyData, StatisticsCache $cache): void
    {
        $prices = collect($allTimeData['prices'])
            ->map(fn ($item) => ['timestamp' => $item[0], 'value' => $item[1]]);

        $pricesSorted = $prices->sortBy('value');

        /** @var array{timestamp: int, value: float} $priceAtl */
        $priceAtl = $pricesSorted->first();
        /** @var array{timestamp: int, value: float} $priceAth */
        $priceAth = $pricesSorted->last();

        $cache->setPriceAtl($priceAtl['timestamp'] / 1000, $priceAtl['value']);
        $cache->setPriceAth($priceAth['timestamp'] / 1000, $priceAth['value']);

        $this->cache52WeekPriceStats($prices, $cache);
        $this->cacheDailyPriceStats($dailyData, $cache);
    }

    private function cache52WeekPriceStats(Collection $data, StatisticsCache $cache): void
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

        $cache->setPriceRange52($priceLow52['value'], $priceHigh52['value']);
    }

    /**
     * @param MarketDataArray $data
     */
    private function cacheDailyPriceStats(array $data, StatisticsCache $cache): void
    {
        $prices = collect($data['prices'])
            ->map(fn ($item) => ['timestamp' => $item[0], 'value' => $item[1]]);

        $priceSorted = $prices->sortBy('value');

        /** @var array{timestamp: int, value: float} $priceDailyLow */
        $priceDailyLow  = $priceSorted->first();
        /** @var array{timestamp: int, value: float} $priceDailyHigh */
        $priceDailyHigh = $priceSorted->last();

        $cache->setPriceRangeDaily($priceDailyLow['value'], $priceDailyHigh['value']);
    }

    /**
     * @param MarketDataArray $data
     */
    private function cacheVolumeStats(array $data, StatisticsCache $cache): void
    {
        $volumes = collect($data['total_volumes'])
            ->map(fn ($item) => ['timestamp' => $item[0], 'value' => $item[1]]);

        $volumeSorted = $volumes->sortBy('value');

        /** @var array{timestamp: int, value: float} $volumeAtl */
        $volumeAtl = $volumeSorted->first();
        /** @var array{timestamp: int, value: float} $volumeAth */
        $volumeAth = $volumeSorted->last();

        $cache->setVolumeAtl($volumeAtl['timestamp'] / 1000, $volumeAtl['value']);
        $cache->setVolumeAth($volumeAth['timestamp'] / 1000, $volumeAth['value']);
    }

    /**
     * @param MarketDataArray $data
     */
    private function cacheMarketCapStats(array $data, StatisticsCache $cache): void
    {
        $marketcaps = collect($data['market_caps'])
            ->map(fn ($item) => ['timestamp' => $item[0], 'value' => $item[1]]);

        $marketCapSorted = $marketcaps->sortBy('value');

        /** @var array{timestamp: int, value: float} $marketCapAtl */
        $marketCapAtl = $marketCapSorted->first();
        /** @var array{timestamp: int, value: float} $marketCapAth */
        $marketCapAth = $marketCapSorted->last();

        $cache->setMarketCapAtl($marketCapAtl['timestamp'] / 1000, $marketCapAtl['value']);
        $cache->setMarketCapAth($marketCapAth['timestamp'] / 1000, $marketCapAth['value']);
    }
}
