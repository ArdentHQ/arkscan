<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\MarketDataProvider;
use App\Facades\Network;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Addresses\Aggregates\HoldingsAggregate;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\Cache\PriceChartCache;
use App\Services\Cache\StatisticsCache;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        $prices = collect($allTimeData['prices'])
            ->map(fn ($item) => ['timestamp' => $item[0], 'value' => $item[1]]);

        $priceAtl = $prices->sortBy('value', SORT_REGULAR, false)->first(); // Price ATL
        $priceAth = $prices->sortBy('value', SORT_REGULAR, true)->first(); // Price ATH

        $cache->setPriceAtl($priceAtl['timestamp'] / 1000, $priceAtl['value']);
        $cache->setPriceAth($priceAth['timestamp'] / 1000, $priceAth['value']);

        $start52WeeksAgo = Carbon::now()->subWeeks(52)->timestamp * 1000;
        $prices52Week = $prices->filter(function ($item) use ($start52WeeksAgo){
            return $item['timestamp'] > $start52WeeksAgo;
        });
        $priceLow52 = $prices52Week->sortBy('value', SORT_REGULAR, false)->first(); // 52 week low
        $priceHigh52 = $prices52Week->sortBy('value', SORT_REGULAR, true)->first(); // 52 week high
        $cache->setPriceRange52($priceLow52['value'], $priceHigh52['value']);

        $dailyData = $marketDataProvider->historicalAll(Network::currency(), 'usd');
        $prices = collect($dailyData['prices'])
            ->map(fn ($item) => ['timestamp' => $item[0], 'value' => $item[1]]);
        $priceDailyLow = $prices->sortBy('value', SORT_REGULAR, false)->first(); // Price Daily low
        $priceDailyHigh = $prices->sortBy('value', SORT_REGULAR, true)->first(); // Price Daily high
        $cache->setPriceRangeDaily($priceDailyLow['value'], $priceDailyHigh['value']);

        $volumes = collect($allTimeData['total_volumes'])
            ->map(fn ($item) => ['timestamp' => $item[0], 'value' => $item[1]]);
        $volumeAtl = $volumes->sortBy('value', SORT_REGULAR, false)->first(); // Volume ATL
        $volumeAth = $volumes->sortBy('value', SORT_REGULAR, true)->first(); // Volume ATH

        $cache->setVolumeAtl($volumeAtl['timestamp'] / 1000, $volumeAtl['value']);
        $cache->setVolumeAth($volumeAth['timestamp'] / 1000, $volumeAth['value']);

        $marketcaps = collect($allTimeData['market_caps'])
            ->map(fn ($item) => ['timestamp' => $item[0], 'value' => $item[1]]);

        $marketCapAtl = $marketcaps->sortBy('value', SORT_REGULAR, false)->first(); // Market Cap ATL
        $marketCapAth = $marketcaps->sortBy('value', SORT_REGULAR, true)->first(); // Market Cap ATH

        $cache->setMarketCapAtl($marketCapAtl['timestamp'] / 1000, $marketCapAtl['value']);
        $cache->setMarketCapAth($marketCapAth['timestamp'] / 1000, $marketCapAth['value']);

    }
}
