<?php

declare(strict_types=1);

use App\Actions\CacheTotalSupply;
use App\Models\Block;
use App\Services\Cache\NetworkCache;

it('should execute the command', function () {
    $block = Block::factory()->create(['height' => 1000]);

    CacheTotalSupply::execute();

    expect((new NetworkCache())->getTotalSupply())->toBe((float) $block->validator->balance->toNumber());
});

it('should execute the command with missing data', function () {
    CacheTotalSupply::execute();

    expect((new NetworkCache())->getTotalSupply())->toBe(0.0);
});
