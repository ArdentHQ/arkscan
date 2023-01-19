<?php

declare(strict_types=1);

use App\Actions\CacheNetworkSupply;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use Illuminate\Support\Facades\Config;

it('should execute the command', function () {
    $block = Block::factory()->create(['height' => 1000]);

    CacheNetworkSupply::execute();

    expect((new NetworkCache())->getSupply())->toBe((float) $block->delegate->balance->toNumber());
});

it('should execute the command with missing data', function () {
    CacheNetworkSupply::execute();

    expect((new NetworkCache())->getSupply())->toBe(0.0);
});

it('should generate based on all wallet balances', function () {
    Config::set('explorer.migration.address', 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj');

    $wallet = Wallet::factory()->create([
        'address' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
        'balance' => 9876543210,
    ]);

    Wallet::factory(10)->create([
        'balance' => 100 * 1e8,
    ]);

    CacheNetworkSupply::execute();

    expect((new NetworkCache())->getSupply())->toBe(1000 * 1e8);
});
