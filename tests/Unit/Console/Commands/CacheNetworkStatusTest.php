<?php

declare(strict_types=1);

use App\Console\Commands\CacheNetworkStatus;
use App\Models\Block;
use App\Services\Cache\NetworkCache;
use function Tests\configureExplorerDatabase;

it('should execute the command', function () {
    configureExplorerDatabase();

    $block = Block::factory()->create(['height' => 1000]);

    (new CacheNetworkStatus())->handle($cache = new NetworkCache());

    expect($cache->getHeight())->toBe(1000);
    expect($cache->getSupply())->toBe((float) $block->delegate->balance->toNumber());
});

it('should execute the command with missing data', function () {
    configureExplorerDatabase();

    (new CacheNetworkStatus())->handle($cache = new NetworkCache());

    expect($cache->getHeight())->toBe(0);
    expect($cache->getSupply())->toBe(0.0);
});
