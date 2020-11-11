<?php

declare(strict_types=1);

use App\Actions\CacheNetworkSupply;
use App\Models\Block;
use App\Services\Cache\NetworkCache;
use function Tests\configureExplorerDatabase;

it('should execute the command', function () {
    configureExplorerDatabase();

    $block = Block::factory()->create(['height' => 1000]);

    CacheNetworkSupply::execute();

    expect((new NetworkCache())->getSupply())->toBe((float) $block->delegate->balance->toNumber());
});

it('should execute the command with missing data', function () {
    configureExplorerDatabase();

    CacheNetworkSupply::execute();

    expect((new NetworkCache())->getSupply())->toBe(0.0);
});
