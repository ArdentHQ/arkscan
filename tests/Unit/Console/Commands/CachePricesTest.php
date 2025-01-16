<?php

declare(strict_types=1);

use App\Console\Commands\CachePrices;
use App\Contracts\MarketDataProvider;
use App\Contracts\Network;
use App\Events\CurrencyUpdate;
use App\Models\Price;
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
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use function Tests\fakeCryptoCompare;

beforeEach(function () {
    $this->travelTo(Carbon::parse('2022-08-18 13:00:00'));

    Price::truncate();
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

    expect(Price::count())->toBe(0);

    app(CachePrices::class)->handle($cryptoCache, $chartsCache, $priceCache, $marketDataProvider);

    expect(Price::count())->toBe(19650);

    foreach (CachePrices::PERIODS as $period) {
        expect($cryptoCache->getPrices('USD.'.$period))->toBeInstanceOf(Collection::class);
        expect($chartsCache->getHistorical('USD', $period))->toBeArray();
    }

    expect($cryptoCache->getPrices('USD.week')->count())->toBe(1310);
})->with(['arkscan.networks.development', 'arkscan.networks.production']);

it('should consolidate historic and recent prices in cache', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);

    fakeCryptoCompare();

    $this->app->singleton(Network::class, fn () => new Blockchain(config('arkscan.networks.production')));

    $cryptoCache        = app(CryptoDataCache::class);
    $chartsCache        = app(PriceChartCache::class);
    $priceCache         = app(PriceCache::class);
    $marketDataProvider = app(MarketDataProvider::class);

    for ($i = 0; $i < 500; $i++) {
        Price::factory()->create([
            'currency'  => 'USD',
            'timestamp' => Carbon::now()->sub('days', $i)->format('Y-m-d 00:00:00'),
        ]);
    }

    expect(Price::count())->toBe(500);

    app(CachePrices::class)->handle($cryptoCache, $chartsCache, $priceCache, $marketDataProvider);

    expect(Price::count())->toBe(19650 + 500);

    foreach (CachePrices::PERIODS as $period) {
        expect($cryptoCache->getPrices('USD.'.$period))->toBeInstanceOf(Collection::class);
        expect($chartsCache->getHistorical('USD', $period))->toBeArray();
    }

    expect($cryptoCache->getPrices('USD.week')->count())->toBe(1310 + 500);
    expect($cryptoCache->getPrices('USD.all')->count())->toBe(1310 + 500);
});

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

    expect(Price::count())->toBe(0);

    (new CachePrices())->handle($cryptoCache, $chartsCache, $priceCache, new CoinGecko());

    expect(Price::count())->toBe(0);

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
        'api.coingecko.com/*' => Http::response(function () {
            throw new ConnectionException();
        }),
    ]);

    $cryptoCache->setPrices('USD.day', collect([1, 2, 3]));
    $chartsCache->setHistorical('USD', 'day', collect([
        '12:00' => 1,
        '13:00' => 2,
        '14:00' => 3,
    ]));

    expect(Price::count())->toBe(0);

    (new CachePrices())->handle($cryptoCache, $chartsCache, $priceCache, new CoinGecko());

    expect(Price::count())->toBe(0);

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

    expect(Price::count())->toBe(0);

    (new CachePrices())->handle($cryptoCache, $chartsCache, $priceCache, new CoinGecko());

    expect(Price::count())->toBe(2); // spans 2 days

    expect($cryptoCache->getPrices('USD.day'))->toEqual(collect($expectedCrypto));
    expect($chartsCache->getHistorical('USD', 'day'))->toEqual($expectedPrices);
});

it('should not have duplicate entries for the current day', function () {
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
    $expectedCrypto = [];
    $expectedPrices = [
        'labels'   => [],
        'datasets' => [],
    ];
    foreach (range(0, 6) as $day) {
        $time     = Carbon::now()->sub($day, 'days');
        $dayStart = $time->copy()->setTime(0, 0, 0);

        $mockPrices[] = [
            Carbon::parse($time)->setTime(0, 0, 0)->valueOf(),
            $day,
        ];

        $expectedCrypto[$dayStart->format('Y-m-d')] = (float) $day;
        $expectedPrices['labels'][]                 = $dayStart->format('d.m');
        $expectedPrices['datasets'][]               = (float) $day;
    }

    $mockPrices[] = [
        $time->valueOf(),
        7,
    ];

    $expectedCrypto[$time->format('Y-m-d')] = 7;
    $expectedPrices['datasets'][6]          = 7;

    Http::fake([
        'https://api.coingecko.com/api/v3/coins/ark/market_chart*' => Http::response([
            'prices' => $mockPrices,
        ], 200),
    ]);

    $cryptoCache->setPrices('USD.week', collect([1, 2, 3]));
    $chartsCache->setHistorical('USD', 'week', collect([
        '12:00' => 1,
        '13:00' => 2,
        '14:00' => 3,
    ]));

    expect(Price::count())->toBe(0);

    (new CachePrices())->handle($cryptoCache, $chartsCache, $priceCache, new CoinGecko());

    expect(Price::count())->toBe(7);

    expect($cryptoCache->getPrices('USD.week'))->toEqual(collect($expectedCrypto));
    expect($chartsCache->getHistorical('USD', 'week'))->toEqual($expectedPrices);
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

    expect(Price::count())->toBe(0);

    (new CachePrices())->handle($cryptoCache, $chartsCache, $priceCache, new CryptoCompare());

    expect(Price::count())->toBe(0);

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
        'cryptocompare.com/*' => Http::response(function () {
            throw new ConnectionException();
        }),
    ]);

    $cryptoCache->setPrices('USD.day', collect([1, 2, 3]));
    $chartsCache->setHistorical('USD', 'day', collect([
        '12:00' => 1,
        '13:00' => 2,
        '14:00' => 3,
    ]));

    expect(Price::count())->toBe(0);

    (new CachePrices())->handle($cryptoCache, $chartsCache, $priceCache, new CryptoCompare());

    expect(Price::count())->toBe(0);

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

    expect(Price::count())->toBe(0);

    (new CachePrices())->handle($cryptoCache, $chartsCache, $priceCache, new CryptoCompare());

    expect(Price::count())->toBe(2); // spans 2 days

    expect($cryptoCache->getPrices('USD.day'))->toEqual(collect($expectedCrypto));
    expect($chartsCache->getHistorical('USD', 'day'))->toEqual($expectedPrices);
});

it('should stop updating prices if a response fails', function () {
    Event::fake();

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

    expect(Price::count())->toBe(0);

    (new CachePrices())->handle($cryptoCache, $chartsCache, $priceCache, new CoinGecko());

    expect(Price::count())->toBe(2); // spans 2 days

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

    Event::assertDispatchedTimes(CurrencyUpdate::class, 1);
    Event::assertDispatched(CurrencyUpdate::class, function ($event) {
        return $event->getId() === 'USD';
    });
});

it('should update oldest currencies first', function () {
    Event::fake();

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

    expect(Price::count())->toBe(0);

    (new CachePrices())->handle($cryptoCache, $chartsCache, $priceCache, new CoinGecko());

    expect(Price::count())->toBe(2); // spans 2 days

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

    Event::assertDispatchedTimes(CurrencyUpdate::class, 1);
    Event::assertDispatched(CurrencyUpdate::class, function ($event) {
        return $event->getId() === 'GBP';
    });
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

    expect(Price::count())->toBe(0);

    (new CachePrices())->handle($cryptoCache, $chartsCache, $priceCache, new CoinGecko());

    expect(Price::count())->toBe(4); // spans 4 days

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
