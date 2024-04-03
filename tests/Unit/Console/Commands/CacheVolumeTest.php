<?php

declare(strict_types=1);

use App\Console\Commands\CacheVolume;
use App\Contracts\Network;
use App\Services\Blockchain\Network as Blockchain;
use App\Services\Cache\CryptoDataCache;
use App\Services\MarketDataProviders\CoinGecko;
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
        'api.coingecko.com/*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true), 200),
    ]);

    $this->app->singleton(Network::class, fn () => new Blockchain(config('arkscan.networks.production')));

    $crypto = app(CryptoDataCache::class);

    app(CacheVolume::class)->handle($crypto, new Coingecko());

    expect($crypto->getVolume('USD'))->toBe('16232625');
    expect($crypto->getVolume('EUR'))->toBe('13740690');
    expect($crypto->getVolume('BTC'))->toBe('355.786');
    expect($crypto->getVolume('ETH'))->toBe('4882');
});

it('should execute the command and exit early when network cannot be exchanged', function () {
    Http::fake([
        'api.coingecko.com/*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true), 200),
    ]);

    $this->app->singleton(Network::class, fn () => new Blockchain(config('arkscan.networks.development')));
    Config::set('arkscan.networks.development.canBeExchanged', false);

    $crypto = app(CryptoDataCache::class);
    $crypto->getCache()->flush();

    app(CacheVolume::class)->handle($crypto, new Coingecko());

    expect($crypto->getVolume('USD'))->toBe(null);
    expect($crypto->getVolume('EUR'))->toBe(null);
    expect($crypto->getVolume('BTC'))->toBe(null);
    expect($crypto->getVolume('ETH'))->toBe(null);
});

it('should not update volume if coingecko returns an empty response', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);

    $crypto = app(CryptoDataCache::class);

    $crypto->getCache()->flush();

    Http::fake([
        'api.coingecko.com/*' => Http::response(null, 200),
    ]);

    $crypto->setVolume('USD', '123');

    (new CacheVolume())->handle($crypto, new CoinGecko());

    expect($crypto->getVolume('USD'))->toEqual('123');
});

it('should not update prices if coingecko throws an exception', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);

    $crypto = app(CryptoDataCache::class);

    $crypto->getCache()->flush();

    Http::fake([
        'api.coingecko.com/*' => Http::response(function () {
            throw new ConnectionException();
        }),
    ]);

    $crypto->setVolume('USD', '123');

    (new CacheVolume())->handle($crypto, new CoinGecko());

    expect($crypto->getVolume('USD'))->toEqual('123');
});

it('should update prices if coingecko does return a response', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);

    Http::fake([
        'api.coingecko.com/*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true), 200),
    ]);

    $this->app->singleton(Network::class, fn () => new Blockchain(config('arkscan.networks.production')));

    $crypto = app(CryptoDataCache::class);

    $crypto->getCache()->flush();
    $crypto->setVolume('USD', '123');

    expect($crypto->getVolume('USD'))->toBe('123');

    app(CacheVolume::class)->handle($crypto, new Coingecko());

    expect($crypto->getVolume('USD'))->toBe('16232625');
    expect($crypto->getVolume('EUR'))->toBe('13740690');
    expect($crypto->getVolume('BTC'))->toBe('355.786');
    expect($crypto->getVolume('ETH'))->toBe('4882');
});
