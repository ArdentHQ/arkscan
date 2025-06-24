<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Transaction;
use App\Services\Blocks\Aggregates\LargestBlockAggregate;

it('should get largest block', function () {
    $block = Block::factory()->create();

    Transaction::factory()->create([
        'value' => 20000 * 1e18,
        'block_hash' => $block->hash,
    ]);

    $largestBlock = Block::factory()->create();

    Transaction::factory()->create([
        'value' => 200000 * 1e18,
        'block_hash' => $largestBlock->hash,
    ]);

    expect((new LargestBlockAggregate())->aggregate()->id)->toBe($largestBlock->id);
});

it('should return null if no records', function () {
    expect((new LargestBlockAggregate())->aggregate())->toBeNull();
});
