<?php

declare(strict_types=1);

use App\Actions\CacheNetworkSupply;
use App\Models\Block;
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
