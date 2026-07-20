<?php

declare(strict_types=1);

use App\Console\Commands\CacheVolume;
use App\Contracts\Network;
use App\Services\Blockchain\Network as Blockchain;
use App\Services\Cache\CryptoDataCache;
use App\Services\MarketDataProviders\ArkPricing;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->travelTo(Carbon::parse('2022-08-18 13:00:00'));
});

it('should execute the command', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);

    Http::fake([
        'ark-pricing.localhost/*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/ark-pricing/market-coin.json')), true), 200),
    ]);

    $this->app->singleton(Network::class, fn () => new Blockchain(config('arkscan.networks.production')));

    $crypto = app(CryptoDataCache::class);

    app(CacheVolume::class)->handle($crypto, new ArkPricing());

    expect($crypto->getVolume('USD'))->toBe('16232625');
    expect($crypto->getVolume('EUR'))->toBe('13740690');
    expect($crypto->getVolume('BTC'))->toBe('355.786');
    expect($crypto->getVolume('ETH'))->toBe('4882');
});

it('should execute the command and exit early when network cannot be exchanged', function () {
    Http::fake([
        'ark-pricing.localhost/*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/ark-pricing/market-coin.json')), true), 200),
    ]);

    $this->app->singleton(Network::class, fn () => new Blockchain(config('arkscan.networks.development')));
    Config::set('arkscan.networks.development.canBeExchanged', false);

    $crypto = app(CryptoDataCache::class);
    $crypto->getCache()->flush();

    app(CacheVolume::class)->handle($crypto, new ArkPricing());

    expect($crypto->getVolume('USD'))->toBe(null);
    expect($crypto->getVolume('EUR'))->toBe(null);
    expect($crypto->getVolume('BTC'))->toBe(null);
    expect($crypto->getVolume('ETH'))->toBe(null);
});

it('should not update volume if ark-pricing returns an empty response', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);

    $crypto = app(CryptoDataCache::class);

    $crypto->getCache()->flush();

    Http::fake([
        'ark-pricing.localhost/*' => Http::response(null, 200),
    ]);

    $crypto->setVolume('USD', '123');

    (new CacheVolume())->handle($crypto, new ArkPricing());

    expect($crypto->getVolume('USD'))->toEqual('123');
});

it('should not update prices if ark-pricing throws an exception', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);

    $crypto = app(CryptoDataCache::class);

    $crypto->getCache()->flush();

    Http::fake([
        'ark-pricing.localhost/*' => fn () => throw new ConnectionException(),
    ]);

    $crypto->setVolume('USD', '123');

    (new CacheVolume())->handle($crypto, new ArkPricing());

    expect($crypto->getVolume('USD'))->toEqual('123');
});

it('should ignore throttled responses from ark-pricing', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);
    Config::set('arkscan.market_data.ark_pricing.exception_frequency', 0);

    $crypto = app(CryptoDataCache::class);

    $crypto->getCache()->flush();

    Http::fake([
        'ark-pricing.localhost/*' => Http::response(['error' => 'Too many requests'], 429),
    ]);

    $crypto->setVolume('USD', '123');

    (new CacheVolume())->handle($crypto, new ArkPricing());

    expect($crypto->getVolume('USD'))->toEqual('123');
});

it('should update prices if ark-pricing does return a response', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);

    Http::fake([
        'ark-pricing.localhost/*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/ark-pricing/market-coin.json')), true), 200),
    ]);

    $this->app->singleton(Network::class, fn () => new Blockchain(config('arkscan.networks.production')));

    $crypto = app(CryptoDataCache::class);

    $crypto->getCache()->flush();
    $crypto->setVolume('USD', '123');

    expect($crypto->getVolume('USD'))->toBe('123');

    app(CacheVolume::class)->handle($crypto, new ArkPricing());

    expect($crypto->getVolume('USD'))->toBe('16232625');
    expect($crypto->getVolume('EUR'))->toBe('13740690');
    expect($crypto->getVolume('BTC'))->toBe('355.786');
    expect($crypto->getVolume('ETH'))->toBe('4882');
});
