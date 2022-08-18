<?php

declare(strict_types=1);

use App\Console\Commands\CachePrices;
use App\Contracts\MarketDataProvider;
use App\Contracts\Network;
use App\Services\Blockchain\Network as Blockchain;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\PriceChartCache;
use App\Services\MarketDataProviders\CoinGecko;
use App\Services\MarketDataProviders\CryptoCompare;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use function Tests\fakeCryptoCompare;

it('should execute the command', function (string $network) {
    fakeCryptoCompare();

    $this->app->singleton(Network::class, fn () => new Blockchain(config($network)));

    $crypto             = app(CryptoDataCache::class);
    $prices             = app(PriceChartCache::class);
    $marketDataProvider = app(MarketDataProvider::class);

    app(CachePrices::class)->handle($crypto, $prices, $marketDataProvider);

    expect($crypto->getPrices('USD'))->toBeInstanceOf(Collection::class);
    expect($prices->getHistorical('USD', 'day'))->toBeArray();
    expect($prices->getHistorical('USD', 'week'))->toBeArray();
    expect($prices->getHistorical('USD', 'month'))->toBeArray();
    expect($prices->getHistorical('USD', 'quarter'))->toBeArray();
    expect($prices->getHistorical('USD', 'year'))->toBeArray();
})->with(['explorer.networks.development', 'explorer.networks.production']);

it('should update prices if coingecko does return a response', function () {
    Config::set('currencies', [
        'usd' => [
            'currency' => 'USD',
            'locale'   => 'en_US',
        ],
    ]);

    $crypto = app(CryptoDataCache::class);
    $prices = app(PriceChartCache::class);

    $crypto->getCache()->flush();
    $prices->getCache()->flush();

    $now = Carbon::now();

    $mockPrices     = [];
    $expectedPrices = [
        'labels'   => [],
        'datasets' => [],
    ];
    foreach (range(0, 23) as $hour) {
        $time = Carbon::now()->sub($hour, 'hours');
        $mockPrices[] = [
            $time->valueOf(),
            $hour,
        ];

        $time->setMinutes(0)
            ->setSeconds(0);

        $expectedCrypto[$time->format('Y-m-d H:00:00')] = (string) $hour;
        $expectedPrices['labels'][]   = $time->format('H:00');
        $expectedPrices['datasets'][] = (float) $hour;
    }

    Http::fake([
        'https://api.coingecko.com/api/v3/coins/ark/market_chart*' => Http::response([
            'prices' => $mockPrices,
        ], 200),
    ]);

    $crypto->setPrices('USD.day', collect([1, 2, 3]));
    $prices->setHistorical('USD', 'day', collect([
        '12:00' => 1,
        '13:00' => 2,
        '14:00' => 3,
    ]));

    (new CachePrices())->handle($crypto, $prices, new CoinGecko());

    expect($crypto->getPrices('USD.day'))->toEqual(collect($expectedCrypto));
    expect($prices->getHistorical('USD', 'day'))->toEqual($expectedPrices);
});

it('should update prices if cryptocompare does return a response', function () {
    Config::set('currencies', [
        'usd' => [
            'currency' => 'USD',
            'locale'   => 'en_US',
        ],
    ]);

    $crypto = app(CryptoDataCache::class);
    $prices = app(PriceChartCache::class);

    $now = Carbon::now();

    $mockPrices     = [];
    $expectedPrices = [
        'labels'   => [],
        'datasets' => [],
    ];
    foreach (range(0, 23) as $hour) {
        $time = Carbon::now()->sub($hour, 'hours');
        $mockPrices[] = [
            'time'  => $time->timestamp,
            'close' => $hour,
        ];

        $time->setMinutes(0)
            ->setSeconds(0);

        $mockCrypto[] = [
            'time'  => $time->timestamp,
            'close' => $hour,
        ];
        $expectedCrypto[$time->format('Y-m-d H:00:00')] = (string) $hour;
        $expectedPrices['labels'][]   = $time->format('H:00');
        $expectedPrices['datasets'][] = (float) $hour;
    }

    Http::fake([
        'https://min-api.cryptocompare.com/data/histoday*' => Http::response([
            'Data' => $mockPrices,
        ], 200),
    ]);

    Http::fake([
        'https://min-api.cryptocompare.com/data/histohour*' => Http::response([
            'Data' => $mockCrypto,
        ], 200),
    ]);

    $crypto->setPrices('USD.day', collect([1, 2, 3]));
    $prices->setHistorical('USD', 'day', collect([
        '12:00' => 1,
        '13:00' => 2,
        '14:00' => 3,
    ]));

    (new CachePrices())->handle($crypto, $prices, new CryptoCompare());

    expect($crypto->getPrices('USD.day'))->toEqual(collect($expectedCrypto));
    expect($prices->getHistorical('USD', 'day'))->toEqual($expectedPrices);
});
