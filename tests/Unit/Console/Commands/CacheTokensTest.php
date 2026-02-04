<?php

declare(strict_types=1);

use App\Console\Commands\CacheTokens;
use App\Models\Token;
use App\Services\Cache\WalletCache;

it('should execute the command', function () {
    (new CacheTokens())->handle();

    $cache = new WalletCache();

    expect($cache->getTokens())->toHaveCount(0);
});

it('should cache contract addresses', function () {
    $cache = new WalletCache();

    expect($cache->getTokens())->toHaveCount(0);

    (new CacheTokens())->handle();

    $tokens = Token::factory(3)->create();

    (new CacheTokens())->handle();

    expect($cache->getTokens()->pluck('address'))->toEqual($tokens->pluck('address'));
    expect($cache->getToken($tokens->first()->address)->symbol)->toEqual($tokens->first()->symbol);
});

it('should use lowercase addresses for keys', function () {
    $cache = new WalletCache();

    $uppercaseToken = Token::factory()->create([
        'address' => '0xABCDEF1234567890ABCDEF1234567890ABCDEF12',
    ]);

    $lowercaseToken = Token::factory()->create([
        'address' => '0xabcdef1234567890abcdef1234567890abcdef12',
    ]);

    (new CacheTokens())->handle();

    expect($cache->getTokens()->keys())->toEqual(collect([
        strtolower($uppercaseToken->address),
        strtolower($lowercaseToken->address),
    ]));

    expect($cache->getToken(strtolower($uppercaseToken->address))->symbol)->toEqual($uppercaseToken->symbol);
    expect($cache->getToken($uppercaseToken->address)->symbol)->toEqual($uppercaseToken->symbol);

    expect($cache->getToken(strtoupper($lowercaseToken->address))->symbol)->toEqual($lowercaseToken->symbol);
    expect($cache->getToken($lowercaseToken->address)->symbol)->toEqual($lowercaseToken->symbol);
});
