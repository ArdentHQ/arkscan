<?php

declare(strict_types=1);

use App\Actions\CacheNetworkHeight;
use App\Models\Block;
use App\Services\Cache\NetworkCache;

it('should execute the command', function () {
    Block::factory()->create(['height' => 1000]);

    CacheNetworkHeight::execute();

    expect((new NetworkCache())->getHeight())->toBe(1000);
});

it('should execute the command with missing data', function () {
    CacheNetworkHeight::execute();

    expect((new NetworkCache())->getHeight())->toBe(0);
});
