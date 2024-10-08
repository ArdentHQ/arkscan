<?php

declare(strict_types=1);

use App\Console\Commands\CacheCurrenciesData;
use App\Contracts\MarketDataProvider;
use App\Contracts\Network as NetworkContract;
use App\Facades\Network;
use App\Services\Blockchain\Network as Blockchain;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\MarketDataProviders\CoinGecko;
use App\Services\MarketDataProviders\CryptoCompare;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use function Tests\fakeCryptoCompare;

it('should execute the command', function () {
    Config::set('currencies.currencies', [
        'usd' => [
            'currency' => 'USD',
            'locale'   => 'en_US',
        ],
    ]);

    fakeCryptoCompare();

    $this->app->singleton(NetworkContract::class, fn () => new Blockchain(config('arkscan.networks.production')));

    $cache              = app(NetworkStatusBlockCache::class);
    $marketDataProvider = app(MarketDataProvider::class);

    expect($cache->getPrice(Network::currency(), 'USD'))->toBeNull();
    expect($cache->getPriceChange(Network::currency(), 'USD'))->toBeNull();

    app(CacheCurrenciesData::class)->handle($cache, $marketDataProvider);

    expect($cache->getPrice(Network::currency(), 'USD'))->toBe(1.2219981765);
    expect($cache->getPriceChange(Network::currency(), 'USD'))->toBe(0.14989143413680925);
});

it('should ignore the cache for development network', function () {
    Config::set('currencies.currencies', [
        'usd' => [
            'currency' => 'USD',
            'locale'   => 'en_US',
        ],
    ]);

    fakeCryptoCompare();

    $this->app->singleton(NetworkContract::class, fn () => new Blockchain(config('arkscan.networks.development')));

    // Set explicitly as phpunit.xml contains ARKSCAN_NETWORK_CAN_BE_EXCHANGED
    Config::set('arkscan.networks.development.canBeExchanged', false);

    $cache              = app(NetworkStatusBlockCache::class);
    $marketDataProvider = app(MarketDataProvider::class);

    expect($cache->getPrice(Network::currency(), 'USD'))->toBeNull();
    expect($cache->getPriceChange(Network::currency(), 'USD'))->toBeNull();

    app(CacheCurrenciesData::class)->handle($cache, $marketDataProvider);

    expect($cache->getPrice(Network::currency(), 'USD'))->toBeNull();
    expect($cache->getPriceChange(Network::currency(), 'USD'))->toBeNull();
});

it('should not update prices if coingecko is down', function () {
    $cache = app(NetworkStatusBlockCache::class);

    Http::fake([
        'api.coingecko.com/*' => Http::response(null, 200),
    ]);

    $cache->setPrice('ARK', 'USD', 15);
    $cache->setPriceChange('ARK', 'USD', 30);

    app(CacheCurrenciesData::class)->handle($cache, new CoinGecko());

    expect($cache->getPrice('ARK', 'USD'))->toEqual(15);
    expect($cache->getPriceChange('ARK', 'USD'))->toEqual(30);
});

it('should not update prices if cryptocompare is down', function () {
    $cache = app(NetworkStatusBlockCache::class);

    Http::fake([
        'cryptocompare.com/*' => Http::response(null, 200),
    ]);

    $cache->setPrice('ARK', 'USD', 15);
    $cache->setPriceChange('ARK', 'USD', 30);

    app(CacheCurrenciesData::class)->handle($cache, new CryptoCompare());

    expect($cache->getPrice('ARK', 'USD'))->toEqual(15);
    expect($cache->getPriceChange('ARK', 'USD'))->toEqual(30);
});
