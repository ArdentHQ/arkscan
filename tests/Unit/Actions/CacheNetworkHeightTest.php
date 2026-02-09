<?php

declare(strict_types=1);

use App\Actions\CacheNetworkHeight;
use App\Models\State;
use App\Services\Cache\NetworkCache;

it('should execute the command', function () {
    State::factory()->create(['block_number' => 1000]);

    CacheNetworkHeight::execute();

    expect((new NetworkCache())->getHeight())->toBe(1000);
});

it('should execute the command with missing data', function () {
    CacheNetworkHeight::execute();

    expect((new NetworkCache())->getHeight())->toBe(0);
});
