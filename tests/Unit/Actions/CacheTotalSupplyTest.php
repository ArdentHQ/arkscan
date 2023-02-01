<?php

declare(strict_types=1);

use App\Actions\CacheTotalSupply;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use Illuminate\Support\Facades\Config;

it('should execute the command', function () {
    $block = Block::factory()->create(['height' => 1000]);

    CacheTotalSupply::execute();

    expect((new NetworkCache())->getTotalSupply())->toBe((float) $block->delegate->balance->toNumber());
});

it('should execute the command with missing data', function () {
    CacheTotalSupply::execute();

    expect((new NetworkCache())->getTotalSupply())->toBe(0.0);
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

    CacheTotalSupply::execute();

    expect((new NetworkCache())->getTotalSupply())->toBe((1000 * 1e8) + 9876543210);
});
