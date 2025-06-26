<?php

declare(strict_types=1);

use App\Console\Commands\CacheContractAddresses;
use App\Models\Receipt;
use App\Services\Cache\WalletCache;

it('should execute the command', function () {
    (new CacheContractAddresses())->handle();

    $cache = new WalletCache();

    expect($cache->getContractAddresses())->toEqual([]);
});

it('should cache contract addresses', function () {
    $cache = new WalletCache();

    expect($cache->getContractAddresses())->toEqual([]);

    (new CacheContractAddresses())->handle();

    Receipt::factory()->create([
        'contract_address' => '0x522B3294E6d06aA25Ad0f1B8891242E335D3B459',
    ]);

    (new CacheContractAddresses())->handle();

    expect($cache->getContractAddresses())->toEqual(['0x522B3294E6d06aA25Ad0f1B8891242E335D3B459']);
});
