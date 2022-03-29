<?php

declare(strict_types=1);

use App\Console\Commands\CacheCurrenciesData;
use App\Contracts\MarketDataProvider;
use App\Contracts\Network as NetworkContract;
use App\Facades\Network;
use App\Services\Blockchain\Network as Blockchain;
use App\Services\Cache\NetworkStatusBlockCache;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use function Tests\fakeCryptoCompare;

it('should execute the command', function () {
    Config::set('currencies', [
        'usd' => [
            'currency' => 'USD',
            'locale'   => 'en_US',
        ],
    ]);

    fakeCryptoCompare();

    $this->app->singleton(NetworkContract::class, fn () => new Blockchain(config('explorer.networks.production')));

    $cache              = app(NetworkStatusBlockCache::class);
    $marketDataProvider = app(MarketDataProvider::class);

    expect($cache->getPrice(Network::currency(), 'USD'))->toBeNull();
    expect($cache->getPriceChange(Network::currency(), 'USD'))->toBeNull();

    app(CacheCurrenciesData::class)->handle($cache, $marketDataProvider);

    expect($cache->getPrice(Network::currency(), 'USD'))->toBe(1.2219981765);
    expect($cache->getPriceChange(Network::currency(), 'USD'))->toBe(0.14989143413680925);
});

it('set values to null when cryptocompare is down', function () {
    Config::set('currencies', [
        'usd' => [
            'currency' => 'USD',
            'locale'   => 'en_US',
        ],
    ]);

    $this->app->singleton(NetworkContract::class, fn () => new Blockchain(config('explorer.networks.production')));

    $cache              = app(NetworkStatusBlockCache::class);
    $marketDataProvider = app(MarketDataProvider::class);

    $cache->setPrice(Network::currency(), 'USD', 1);
    $cache->setPriceChange(Network::currency(), 'USD', 1);

    Http::fake([
        'cryptocompare.com/*' => function () {
            throw new ConnectionException();
        },
    ]);

    app(CacheCurrenciesData::class)->handle($cache, $marketDataProvider);

    expect($cache->getPrice(Network::currency(), 'USD'))->toBeNull();
    expect($cache->getPriceChange(Network::currency(), 'USD'))->toBeNull();
});

it('should ignore the cache for development network', function () {
    Config::set('currencies', [
        'usd' => [
            'currency' => 'USD',
            'locale'   => 'en_US',
        ],
    ]);

    fakeCryptoCompare();

    $this->app->singleton(NetworkContract::class, fn () => new Blockchain(config('explorer.networks.development')));

    $cache              = app(NetworkStatusBlockCache::class);
    $marketDataProvider = app(MarketDataProvider::class);

    expect($cache->getPrice(Network::currency(), 'USD'))->toBeNull();
    expect($cache->getPriceChange(Network::currency(), 'USD'))->toBeNull();

    app(CacheCurrenciesData::class)->handle($cache, $marketDataProvider);

    expect($cache->getPrice(Network::currency(), 'USD'))->toBeNull();
    expect($cache->getPriceChange(Network::currency(), 'USD'))->toBeNull();
});
