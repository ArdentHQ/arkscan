<?php

declare(strict_types=1);

use App\Console\Commands\CachePrices;
use App\Contracts\MarketDataProvider;
use App\Contracts\Network;
use App\Services\Blockchain\Network as Blockchain;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\PriceCache;
use App\Services\Cache\PriceChartCache;
use App\Services\MarketDataProviders\CoinGecko;
use App\Services\MarketDataProviders\CryptoCompare;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use function Tests\fakeCryptoCompare;

beforeEach(function () {
    $this->travelTo(Carbon::parse('2022-08-18 13:00:00'));
});

function generateMockPrices(&$expectedCrypto, &$expectedPrices): array
{
    $expectedPrices = [
        'labels'   => [],
        'datasets' => [],
    ];

    $prices = [];
    foreach (range(0, 23) as $hour) {
        $time        = Carbon::now()->sub($hour, 'hours');
        $prices[]    = [
            $time->valueOf(),
            $hour,
        ];

        $time->setMinutes(0)
            ->setSeconds(0);

        $expectedCrypto[$time->format('Y-m-d H:00:00')] = (string) $hour;
        $expectedPrices['labels'][]                     = $time->format('H:00');
        $expectedPrices['datasets'][]                   = (float) $hour;
    }

    return $prices;
}

it('should execute the command', function (string $network) {
    Config::set('arkscan.networks.development.canBeExchanged', true);

    fakeCryptoCompare();

    $this->app->singleton(Network::class, fn () => new Blockchain(config($network)));

    $cryptoCache        = app(CryptoDataCache::class);
    $chartsCache        = app(PriceChartCache::class);
    $priceCache         = app(PriceCache::class);
    $marketDataProvider = app(MarketDataProvider::class);

    app(CachePrices::class)->handle($cryptoCache, $chartsCache, $priceCache, $marketDataProvider);

    expect($cryptoCache->getPrices('USD'))->toBeInstanceOf(Collection::class);
    expect($chartsCache->getHistorical('USD', 'day'))->toBeArray();
    expect($chartsCache->getHistorical('USD', 'week'))->toBeArray();
    expect($chartsCache->getHistorical('USD', 'month'))->toBeArray();
    expect($chartsCache->getHistorical('USD', 'quarter'))->toBeArray();
    expect($chartsCache->getHistorical('USD', 'year'))->toBeArray();
})->with(['arkscan.networks.development', 'arkscan.networks.production']);

it('should not update prices if coingecko returns an empty response', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);

    $cryptoCache = app(CryptoDataCache::class);
    $chartsCache = app(PriceChartCache::class);
    $priceCache  = app(PriceCache::class);

    $cryptoCache->getCache()->flush();
    $chartsCache->getCache()->flush();
    $priceCache->getCache()->flush();

    Http::fake([
        'api.coingecko.com/*' => Http::response(null, 200),
    ]);

    $cryptoCache->setPrices('USD.day', collect([1, 2, 3]));
    $chartsCache->setHistorical('USD', 'day', collect([
        '12:00' => 1,
        '13:00' => 2,
        '14:00' => 3,
    ]));

    (new CachePrices())->handle($cryptoCache, $chartsCache, $priceCache, new CoinGecko());

    expect($cryptoCache->getPrices('USD.day'))->toEqual(collect([1, 2, 3]));
    expect($chartsCache->getHistorical('USD', 'day'))->toEqual([
        'labels'   => [
            '12:00',
            '13:00',
            '14:00',
        ],
        'datasets' => [
            1,
            2,
            3,
        ],
    ]);
});

it('should not update prices if coingecko throws an exception', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);

    $cryptoCache = app(CryptoDataCache::class);
    $chartsCache = app(PriceChartCache::class);
    $priceCache  = app(PriceCache::class);

    $cryptoCache->getCache()->flush();
    $chartsCache->getCache()->flush();
    $priceCache->getCache()->flush();

    Http::fake([
        'api.coingecko.com/*' => function () {
            throw new ConnectionException();
        },
    ]);

    $cryptoCache->setPrices('USD.day', collect([1, 2, 3]));
    $chartsCache->setHistorical('USD', 'day', collect([
        '12:00' => 1,
        '13:00' => 2,
        '14:00' => 3,
    ]));

    (new CachePrices())->handle($cryptoCache, $chartsCache, $priceCache, new CoinGecko());

    expect($cryptoCache->getPrices('USD.day'))->toEqual(collect([1, 2, 3]));
    expect($chartsCache->getHistorical('USD', 'day'))->toEqual([
        'labels'   => [
            '12:00',
            '13:00',
            '14:00',
        ],
        'datasets' => [
            1,
            2,
            3,
        ],
    ]);
});

it('should update prices if coingecko does return a response', function () {
    Config::set('currencies', [
        'usd' => [
            'currency' => 'USD',
            'locale'   => 'en_US',
        ],
    ]);

    Config::set('arkscan.network', 'production');

    $cryptoCache = app(CryptoDataCache::class);
    $chartsCache = app(PriceChartCache::class);
    $priceCache  = app(PriceCache::class);

    $cryptoCache->getCache()->flush();
    $chartsCache->getCache()->flush();
    $priceCache->getCache()->flush();

    $now = Carbon::now();

    $mockPrices = generateMockPrices($expectedCrypto, $expectedPrices);

    Http::fake([
        'https://api.coingecko.com/api/v3/coins/ark/market_chart*' => Http::response([
            'prices' => $mockPrices,
        ], 200),
    ]);

    $cryptoCache->setPrices('USD.day', collect([1, 2, 3]));
    $chartsCache->setHistorical('USD', 'day', collect([
        '12:00' => 1,
        '13:00' => 2,
        '14:00' => 3,
    ]));

    (new CachePrices())->handle($cryptoCache, $chartsCache, $priceCache, new CoinGecko());

    expect($cryptoCache->getPrices('USD.day'))->toEqual(collect($expectedCrypto));
    expect($chartsCache->getHistorical('USD', 'day'))->toEqual($expectedPrices);
});

it('should not update prices if cryptocompare returns an empty response', function () {
    $cryptoCache = app(CryptoDataCache::class);
    $chartsCache = app(PriceChartCache::class);
    $priceCache  = app(PriceCache::class);

    $cryptoCache->getCache()->flush();
    $chartsCache->getCache()->flush();
    $priceCache->getCache()->flush();

    Http::fake([
        'https://min-api.cryptocompare.com/data/*' => Http::response([
            'Response' => 'Success',
            'Data'     => [],
        ], 200),
    ]);

    $cryptoCache->setPrices('USD.day', collect([1, 2, 3]));
    $chartsCache->setHistorical('USD', 'day', collect([
        '12:00' => 1,
        '13:00' => 2,
        '14:00' => 3,
    ]));

    (new CachePrices())->handle($cryptoCache, $chartsCache, $priceCache, new CryptoCompare());

    expect($cryptoCache->getPrices('USD.day'))->toEqual(collect([1, 2, 3]));
    expect($chartsCache->getHistorical('USD', 'day'))->toEqual([
        'labels'   => [
            '12:00',
            '13:00',
            '14:00',
        ],
        'datasets' => [
            1,
            2,
            3,
        ],
    ]);
});

it('should not update prices if cryptocompare throws an exception', function () {
    $cryptoCache = app(CryptoDataCache::class);
    $chartsCache = app(PriceChartCache::class);
    $priceCache  = app(PriceCache::class);

    $cryptoCache->getCache()->flush();
    $chartsCache->getCache()->flush();
    $priceCache->getCache()->flush();

    Http::fake([
        'cryptocompare.com/*' => function () {
            throw new ConnectionException();
        },
    ]);

    $cryptoCache->setPrices('USD.day', collect([1, 2, 3]));
    $chartsCache->setHistorical('USD', 'day', collect([
        '12:00' => 1,
        '13:00' => 2,
        '14:00' => 3,
    ]));

    (new CachePrices())->handle($cryptoCache, $chartsCache, $priceCache, new CryptoCompare());

    expect($cryptoCache->getPrices('USD.day'))->toEqual(collect([1, 2, 3]));
    expect($chartsCache->getHistorical('USD', 'day'))->toEqual([
        'labels'   => [
            '12:00',
            '13:00',
            '14:00',
        ],
        'datasets' => [
            1,
            2,
            3,
        ],
    ]);
});

it('should update prices if cryptocompare does return a response', function () {
    Config::set('currencies', [
        'usd' => [
            'currency' => 'USD',
            'locale'   => 'en_US',
        ],
    ]);

    Config::set('arkscan.network', 'production');

    $cryptoCache = app(CryptoDataCache::class);
    $chartsCache = app(PriceChartCache::class);
    $priceCache  = app(PriceCache::class);

    $cryptoCache->getCache()->flush();
    $chartsCache->getCache()->flush();
    $priceCache->getCache()->flush();

    $mockPrices     = [];
    $expectedPrices = [
        'labels'   => [],
        'datasets' => [],
    ];
    foreach (range(0, 23) as $hour) {
        $time         = Carbon::now()->sub($hour, 'hours');
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
        $expectedPrices['labels'][]                     = $time->format('H:00');
        $expectedPrices['datasets'][]                   = (float) $hour;
    }

    Http::fake([
        'https://min-api.cryptocompare.com/data/histoday*' => Http::response([
            'Response' => 'Success',
            'Data'     => $mockPrices,
        ], 200),
    ]);

    Http::fake([
        'https://min-api.cryptocompare.com/data/histohour*' => Http::response([
            'Response' => 'Success',
            'Data'     => $mockCrypto,
        ], 200),
    ]);

    $cryptoCache->setPrices('USD.day', collect([1, 2, 3]));
    $chartsCache->setHistorical('USD', 'day', collect([
        '12:00' => 1,
        '13:00' => 2,
        '14:00' => 3,
    ]));

    (new CachePrices())->handle($cryptoCache, $chartsCache, $priceCache, new CryptoCompare());

    expect($cryptoCache->getPrices('USD.day'))->toEqual(collect($expectedCrypto));
    expect($chartsCache->getHistorical('USD', 'day'))->toEqual($expectedPrices);
});

it('should stop updating prices if a response fails', function () {
    Config::set('currencies', [
        'usd' => [
            'currency' => 'USD',
            'locale'   => 'en_US',
        ],
        'gbp' => [
            'currency' => 'GBP',
            'locale'   => 'en_GB',
        ],
        'eur' => [
            'currency' => 'EUR',
            'locale'   => 'en_NL',
        ],
    ]);

    Config::set('arkscan.network', 'production');

    $cryptoCache = app(CryptoDataCache::class);
    $chartsCache = app(PriceChartCache::class);
    $priceCache  = app(PriceCache::class);

    $cryptoCache->getCache()->flush();
    $chartsCache->getCache()->flush();
    $priceCache->getCache()->flush();

    $usdPrices = generateMockPrices($expectedCrypto, $expectedPrices);

    Http::fakeSequence('api.coingecko.com/*')
        ->push(['prices' => $usdPrices], 200)
        ->push(['prices' => $usdPrices], 200) // Second time is for the historicalHourly call
        ->push(null, 200)
        ->push(null, 200)
        ->push(null, 200)
        ->push(null, 200);

    foreach (config('currencies') as $currency) {
        $cryptoCache->setPrices($currency['currency'].'.day', collect([1, 2, 3]));
        $chartsCache->setHistorical($currency['currency'], 'day', collect([
            '12:00' => 1,
            '13:00' => 2,
            '14:00' => 3,
        ]));
    }

    $this->freezeTime();

    (new CachePrices())->handle($cryptoCache, $chartsCache, $priceCache, new CoinGecko());

    expect($priceCache->getLastUpdated())->toBe([
        'USD' => Carbon::now()->unix(),
    ]);

    expect($cryptoCache->getPrices('USD.day'))->toEqual(collect($expectedCrypto));
    expect($chartsCache->getHistorical('USD', 'day'))->toEqual($expectedPrices);

    foreach (['GBP', 'EUR'] as $currency) {
        expect($cryptoCache->getPrices($currency.'.day'))->toEqual(collect([1, 2, 3]));
        expect($chartsCache->getHistorical($currency, 'day'))->toEqual([
            'labels'   => [
                '12:00',
                '13:00',
                '14:00',
            ],
            'datasets' => [
                1,
                2,
                3,
            ],
        ]);
    }
});

it('should update oldest currencies first', function () {
    Config::set('currencies', [
        'usd' => [
            'currency' => 'USD',
            'locale'   => 'en_US',
        ],
        'gbp' => [
            'currency' => 'GBP',
            'locale'   => 'en_GB',
        ],
        'eur' => [
            'currency' => 'EUR',
            'locale'   => 'en_NL',
        ],
    ]);

    Config::set('arkscan.network', 'production');

    $cryptoCache = app(CryptoDataCache::class);
    $chartsCache = app(PriceChartCache::class);
    $priceCache  = app(PriceCache::class);

    $cryptoCache->getCache()->flush();
    $chartsCache->getCache()->flush();
    $priceCache->getCache()->flush();

    $this->freezeTime();

    $priceCache->setLastUpdated([
        'USD' => Carbon::parse('2023-04-05')->unix(),
    ]);

    $gbpPrices = generateMockPrices($expectedCrypto, $expectedPrices);

    Http::fakeSequence('api.coingecko.com/*')
        ->push(['prices' => $gbpPrices], 200)
        ->push(['prices' => $gbpPrices], 200) // Second time is for the historicalHourly call
        ->push(null, 200)
        ->push(null, 200)
        ->push(null, 200)
        ->push(null, 200);

    $cryptoCache->setPrices('USD.day', collect([1, 2, 3]));
    $chartsCache->setHistorical('USD', 'day', collect([
        '12:00' => 1,
        '13:00' => 2,
        '14:00' => 3,
    ]));

    (new CachePrices())->handle($cryptoCache, $chartsCache, $priceCache, new CoinGecko());

    expect($priceCache->getLastUpdated())->toBe([
        'USD' => Carbon::parse('2023-04-05')->unix(),
        'GBP' => Carbon::now()->unix(),
    ]);

    expect($cryptoCache->getPrices('GBP.day'))->toEqual(collect($expectedCrypto));
    expect($chartsCache->getHistorical('GBP', 'day'))->toEqual($expectedPrices);

    expect($cryptoCache->getPrices('USD.day'))->toEqual(collect([1, 2, 3]));
    expect($chartsCache->getHistorical('USD', 'day'))->toEqual([
        'labels'   => [
            '12:00',
            '13:00',
            '14:00',
        ],
        'datasets' => [
            1,
            2,
            3,
        ],
    ]);

    expect($cryptoCache->getPrices('EUR.day'))->toEqual(collect([]));
    expect($chartsCache->getHistorical('EUR', 'day'))->toEqual([]);
});

it('should not update if updated within 10 minutes', function () {
    Config::set('currencies', [
        'usd' => [
            'currency' => 'USD',
            'locale'   => 'en_US',
        ],
        'gbp' => [
            'currency' => 'GBP',
            'locale'   => 'en_GB',
        ],
        'eur' => [
            'currency' => 'EUR',
            'locale'   => 'en_NL',
        ],
    ]);

    Config::set('arkscan.network', 'production');

    $cryptoCache = app(CryptoDataCache::class);
    $chartsCache = app(PriceChartCache::class);
    $priceCache  = app(PriceCache::class);

    $cryptoCache->getCache()->flush();
    $chartsCache->getCache()->flush();
    $priceCache->getCache()->flush();

    $this->freezeTime();

    $priceCache->setLastUpdated([
        'USD' => Carbon::now()->sub('minutes', 8)->unix(),
        'GBP' => Carbon::now()->sub('minutes', 11)->unix(),
    ]);

    $gbpPrices = generateMockPrices($expectedGbpCrypto, $expectedGbpPrices);
    $eurPrices = generateMockPrices($expectedEurCrypto, $expectedEurPrices);

    Http::fakeSequence('api.coingecko.com/*')
        ->push(['prices' => $eurPrices], 200)
        ->push(['prices' => $eurPrices], 200)
        ->push(['prices' => $gbpPrices], 200)
        ->push(['prices' => $gbpPrices], 200); // Second time is for the historicalHourly call

    $cryptoCache->setPrices('USD.day', collect([1, 2, 3]));
    $chartsCache->setHistorical('USD', 'day', collect([
        '12:00' => 1,
        '13:00' => 2,
        '14:00' => 3,
    ]));

    (new CachePrices())->handle($cryptoCache, $chartsCache, $priceCache, new CoinGecko());

    expect($priceCache->getLastUpdated())->toBe([
        'USD' => Carbon::now()->sub('minutes', 8)->unix(),
        'GBP' => Carbon::now()->unix(),
        'EUR' => Carbon::now()->unix(),
    ]);

    expect($cryptoCache->getPrices('EUR.day'))->toEqual(collect($expectedEurCrypto));
    expect($chartsCache->getHistorical('EUR', 'day'))->toEqual($expectedEurPrices);

    expect($cryptoCache->getPrices('GBP.day'))->toEqual(collect($expectedGbpCrypto));
    expect($chartsCache->getHistorical('GBP', 'day'))->toEqual($expectedGbpPrices);

    expect($cryptoCache->getPrices('USD.day'))->toEqual(collect([1, 2, 3]));
    expect($chartsCache->getHistorical('USD', 'day'))->toEqual([
        'labels'   => [
            '12:00',
            '13:00',
            '14:00',
        ],
        'datasets' => [
            1,
            2,
            3,
        ],
    ]);
});
