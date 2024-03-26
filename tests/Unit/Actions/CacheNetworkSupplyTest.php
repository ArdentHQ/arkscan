<?php

declare(strict_types=1);

use App\Actions\CacheNetworkSupply;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;

it('should execute the command', function () {
    $block = Block::factory()->create(['height' => 1000]);

    CacheNetworkSupply::execute();

    expect((new NetworkCache())->getSupply())->toBe((float) $block->delegate->balance->toNumber());
});

it('should execute the command with missing data', function () {
    CacheNetworkSupply::execute();

    expect((new NetworkCache())->getSupply())->toBe(0.0);
});

it('should sum all wallets', function () {
    Wallet::factory(50)->create([
        'balance' => 1000 * 1e8,
    ]);

    CacheNetworkSupply::execute();

    expect((new NetworkCache())->getSupply())->toBe(50000 * 1e8);
});
