<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Transaction;
use App\Services\Blocks\Aggregates\LargestBlockAggregate;

it('should get largest transaction', function () {
    $largestBlock = Block::factory()->create();
    $otherBlock   = Block::factory()->create();

    Transaction::factory(1)->transfer()->create([
        'amount'   => 20000 * 1e8,
        'fee'      => 10 * 1e8,
        'block_id' => $largestBlock->id,
    ]);

    Transaction::factory(20)->transfer()->create([
        'amount'   => 60 * 1e8,
        'fee'      => 10 * 1e8,
        'block_id' => $otherBlock->id,
    ]);

    expect((new LargestBlockAggregate())->aggregate()->id)->toBe($largestBlock->id);
});

it('should return null if no records', function () {
    expect((new LargestBlockAggregate())->aggregate())->toBeNull();
});
