<?php

declare(strict_types=1);

use App\Models\Block;
use App\Services\Blocks\Aggregates\LargestBlockAggregate;

it('should get largest block', function () {
    Block::factory()->create(['total_amount' => 20000 * 1e8]);
    $largestBlock = Block::factory()->create(['total_amount' => 200000 * 1e8]);

    expect((new LargestBlockAggregate())->aggregate()->id)->toBe($largestBlock->id);
});

it('should return null if no records', function () {
    expect((new LargestBlockAggregate())->aggregate())->toBeNull();
});
