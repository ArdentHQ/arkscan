<?php

use App\Models\Block;
use App\Services\Blocks\Aggregates\HighestBlockFeeAggregate;

it('should get largest transaction', function () {
    $largestBlock = Block::factory()->create(['total_fee' => 30 * 1e8]);

    Block::factory()->create(['total_fee' => 1 * 1e8]);

    expect((new HighestBlockFeeAggregate())->aggregate()->id)->toBe($largestBlock->id);
});

it('should return null if no records', function () {
    expect((new HighestBlockFeeAggregate())->aggregate())->toBeNull();
});
