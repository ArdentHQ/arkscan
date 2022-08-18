<?php

declare(strict_types=1);

use App\Contracts\Network as NetworkContract;
use App\Facades\Network;
use App\Jobs\CacheCurrenciesHistory;
use App\Services\Blockchain\Network as Blockchain;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\MarketDataProviders\CoinGecko;
use App\Services\MarketDataProviders\CryptoCompare;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use function Tests\fakeCryptoCompare;

it('should cache the history', function () {
    Config::set('explorer.networks.development.canBeExchanged', true);

    fakeCryptoCompare();

    $this->app->singleton(NetworkContract::class, fn () => new Blockchain(config('explorer.networks.production')));

    $cache = new NetworkStatusBlockCache();

    expect($cache->getHistoricalHourly(Network::currency(), 'USD'))->toBeNull();

    CacheCurrenciesHistory::dispatch('ARK', 'USD');

    expect($cache->getHistoricalHourly(Network::currency(), 'USD'))->toEqual(collect([
        '2021-05-18 18:00:00' => '1.898',
        '2021-05-18 19:00:00' => '1.904',
        '2021-05-18 20:00:00' => '1.967',
        '2021-05-18 21:00:00' => '1.941',
        '2021-05-18 22:00:00' => '2.013',
        '2021-05-18 23:00:00' => '2.213',
        '2021-05-19 00:00:00' => '2.414',
        '2021-05-19 01:00:00' => '2.369',
        '2021-05-19 02:00:00' => '2.469',
        '2021-05-19 03:00:00' => '2.374',
        '2021-05-19 04:00:00' => '2.228',
        '2021-05-19 05:00:00' => '2.211',
        '2021-05-19 06:00:00' => '2.266',
        '2021-05-19 07:00:00' => '2.364',
        '2021-05-19 08:00:00' => '2.341',
        '2021-05-19 09:00:00' => '2.269',
        '2021-05-19 10:00:00' => '1.981',
        '2021-05-19 11:00:00' => '1.889',
        '2021-05-19 12:00:00' => '1.275',
        '2021-05-19 13:00:00' => '1.471',
        '2021-05-19 14:00:00' => '1.498',
        '2021-05-19 15:00:00' => '1.518',
        '2021-05-19 16:00:00' => '1.61',
        '2021-05-19 17:00:00' => '1.638',
    ]));
});

it('should not update prices if coingecko returns an empty response', function () {
    Config::set('explorer.networks.development.canBeExchanged', true);

    $cache = app(NetworkStatusBlockCache::class);

    Http::fake([
        'api.coingecko.com/*' => Http::response(null, 200),
    ]);

    $cache->setHistoricalHourly('ARK', 'USD', collect([1, 2, 3]));

    (new CacheCurrenciesHistory('ARK', 'USD'))->handle($cache, new CoinGecko());

    expect($cache->getHistoricalHourly('ARK', 'USD'))->toEqual(collect([1, 2, 3]));
});

it('should not update prices if coingecko throws an exception', function () {
    Config::set('explorer.networks.development.canBeExchanged', true);

    $cache = app(NetworkStatusBlockCache::class);

    Http::fake([
        'api.coingecko.com/*' => function () {
            throw new ConnectionException();
        },
    ]);

    $cache->setHistoricalHourly('ARK', 'USD', collect([1, 2, 3]));

    (new CacheCurrenciesHistory('ARK', 'USD'))->handle($cache, new CoinGecko());

    expect($cache->getHistoricalHourly('ARK', 'USD'))->toEqual(collect([1, 2, 3]));
});

it('should update prices if coingecko does return a response', function () {
    Config::set('explorer.networks.development.canBeExchanged', true);

    $cache = app(NetworkStatusBlockCache::class);

    $now = Carbon::now();

    $mockPrices     = [];
    $expectedPrices = [];
    foreach (range(0, 23) as $hour) {
        $time         = $now->sub($hour, 'hours');
        $mockPrices[] = [
            $time->valueOf(),
            $hour,
        ];
        $expectedPrices[$time->format('Y-m-d H:00:00')] = $hour;
    }

    Http::fake([
        'api.coingecko.com/*' => Http::response([
            'prices' => $mockPrices,
        ], 200),
    ]);

    $cache->setHistoricalHourly('ARK', 'USD', collect([1, 2, 3]));

    (new CacheCurrenciesHistory('ARK', 'USD'))->handle($cache, new CoinGecko());

    expect($cache->getHistoricalHourly('ARK', 'USD'))->toEqual(collect($expectedPrices));
});

it('should not update prices if cryptocompare throws an exception', function () {
    Config::set('explorer.networks.development.canBeExchanged', true);

    $cache = app(NetworkStatusBlockCache::class);

    Http::fake([
        'cryptocompare.com/*' => function () {
            throw new ConnectionException();
        },
    ]);

    $cache->setHistoricalHourly('ARK', 'USD', collect([1, 2, 3]));

    try {
        (new CacheCurrenciesHistory('ARK', 'USD'))->handle($cache, new CryptoCompare());
    } catch (ConnectionException $e) {
        expect($cache->getHistoricalHourly('ARK', 'USD'))->toEqual(collect([1, 2, 3]));
    }
});

it('should update prices if cryptocompare does return a response', function () {
    Config::set('explorer.networks.development.canBeExchanged', true);

    $cache = app(NetworkStatusBlockCache::class);

    $now = Carbon::now();

    $mockPrices     = [];
    $expectedPrices = [];
    foreach (range(0, 23) as $hour) {
        $time         = $now->sub($hour, 'hours');
        $mockPrices[] = [
            'time'  => $time->timestamp,
            'close' => $hour,
        ];
        $expectedPrices[$time->format('Y-m-d H:i:s')] = (string) $hour;
    }

    Http::fake([
        'cryptocompare.com/*' => Http::response([
            'Data' => $mockPrices,
        ], 200),
    ]);

    $cache->setHistoricalHourly('ARK', 'USD', collect([1, 2, 3]));

    (new CacheCurrenciesHistory('ARK', 'USD'))->handle($cache, new CryptoCompare());

    expect($cache->getHistoricalHourly('ARK', 'USD'))->toEqual(collect($expectedPrices));
});
