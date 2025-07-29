<?php

declare(strict_types=1);

use App\Models\Block;
use App\Services\Blocks\Aggregates\HighestBlockFeeAggregate;

it('should get largest transaction', function () {
    $largestBlock = Block::factory()->create(['fee' => 30 * 1e18]);

    Block::factory()->create(['fee' => 1 * 1e18]);

    expect((new HighestBlockFeeAggregate())->aggregate()->id)->toBe($largestBlock->id);
});

it('should return null if no records', function () {
    expect((new HighestBlockFeeAggregate())->aggregate())->toBeNull();
});
