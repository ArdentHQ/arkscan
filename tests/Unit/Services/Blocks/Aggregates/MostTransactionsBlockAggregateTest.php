<?php

use App\Models\Block;
use App\Services\Blocks\Aggregates\MostTransactionsBlockAggregate;

it('should get largest transaction', function () {
    $largestBlock = Block::factory()->create(['number_of_transactions' => 30]);

    Block::factory()->create(['number_of_transactions' => 1]);

    expect((new MostTransactionsBlockAggregate())->aggregate()->id)->toBe($largestBlock->id);
});

it('should return null if no records', function () {
    expect((new MostTransactionsBlockAggregate())->aggregate())->toBeNull();
});
