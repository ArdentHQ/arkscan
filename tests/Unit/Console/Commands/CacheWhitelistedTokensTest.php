<?php

declare(strict_types=1);

use App\Services\Cache\WalletCache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

it('should cache whitelisted token addresses', function () {
    Config::set('arkscan.networks.development.whitelistedTokens', 'https://example.com/tokens-whitelist.json');

    Http::fake(Http::response([
        ['address' => '0xABCDEF1234567890ABCDEF1234567890ABCDEF12', 'comment' => 'Token A', 'createdAt' => '2026-03-02T15:00:00.000Z'],
        ['address' => '0x1234567890abcdef1234567890abcdef12345678', 'comment' => 'Token B', 'createdAt' => '2026-03-02T15:00:00.000Z'],
    ], 200));

    Artisan::call('explorer:cache-whitelisted-tokens');

    $cache = new WalletCache();

    expect($cache->getWhitelistedTokens())->toBe([
        '0xabcdef1234567890abcdef1234567890abcdef12',
        '0x1234567890abcdef1234567890abcdef12345678',
    ]);
});

it('should lowercase all addresses', function () {
    Config::set('arkscan.networks.development.whitelistedTokens', 'https://example.com/tokens-whitelist.json');

    Http::fake(Http::response([
        ['address' => '0xAABBCC', 'comment' => 'Test', 'createdAt' => '2026-03-02T15:00:00.000Z'],
    ], 200));

    Artisan::call('explorer:cache-whitelisted-tokens');

    $cache = new WalletCache();

    expect($cache->getWhitelistedTokens())->toBe(['0xaabbcc']);
});

it('should skip when url is not configured', function () {
    Config::set('arkscan.networks.development.whitelistedTokens', null);

    Http::fake();

    Artisan::call('explorer:cache-whitelisted-tokens');

    $cache = new WalletCache();

    expect($cache->getWhitelistedTokens())->toBe([]);

    Http::assertNothingSent();
});

it('should not refetch when cache is fresh', function () {
    Config::set('arkscan.networks.development.whitelistedTokens', 'https://example.com/tokens-whitelist.json');

    $cache = new WalletCache();
    $cache->setWhitelistedTokens(fn () => ['0xaaa', '0xbbb']);

    Http::fake(Http::response([
        ['address' => '0xCCC', 'comment' => 'New', 'createdAt' => '2026-03-02T15:00:00.000Z'],
    ], 200));

    Artisan::call('explorer:cache-whitelisted-tokens');

    expect($cache->getWhitelistedTokens())->toBe(['0xaaa', '0xbbb']);

    Http::assertNothingSent();
});

it('should force update whitelisted tokens', function () {
    Config::set('arkscan.networks.development.whitelistedTokens', 'https://example.com/tokens-whitelist.json');

    $cache = new WalletCache();
    $cache->setWhitelistedTokens(fn () => ['0xold']);

    Http::fake(Http::response([
        ['address' => '0xNEW', 'comment' => 'New Token', 'createdAt' => '2026-03-02T15:00:00.000Z'],
    ], 200));

    Artisan::call('explorer:cache-whitelisted-tokens', ['--force' => true]);

    expect($cache->getWhitelistedTokens())->toBe(['0xnew']);
});
