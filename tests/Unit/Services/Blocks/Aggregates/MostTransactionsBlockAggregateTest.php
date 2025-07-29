<?php

declare(strict_types=1);

use App\Models\Block;
use App\Services\Blocks\Aggregates\MostTransactionsBlockAggregate;

it('should get largest transaction', function () {
    $largestBlock = Block::factory()->create(['transactions_count' => 30]);

    Block::factory()->create(['transactions_count' => 1]);

    expect((new MostTransactionsBlockAggregate())->aggregate()->id)->toBe($largestBlock->id);
});

it('should return null if no records', function () {
    expect((new MostTransactionsBlockAggregate())->aggregate())->toBeNull();
});
