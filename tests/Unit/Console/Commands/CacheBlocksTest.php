<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Transaction;
use App\Services\Cache\BlockCache;

it('should cache largest block by amount', function () {
    $cache = new BlockCache();

    Transaction::factory()->create(['amount' => 0]);

    $largestBlock = Block::factory()->create([
        'total_amount' => 1000 * 1e8,
    ]);

    Block::factory()->create([
        'total_amount' => 0,
    ]);

    $this->artisan('explorer:cache-blocks');

    expect($cache->getLargestIdByAmount())->toBe($largestBlock->id);
});

it('should cache largest block by fee', function () {
    $cache = new BlockCache();

    Block::factory()->create(['total_fee' => 0]);

    $largestBlock = Block::factory()->create(['total_fee' => 100 * 1e8]);

    Block::factory()->create(['total_fee' => 0]);

    $this->artisan('explorer:cache-blocks');

    expect($cache->getLargestIdByFees())->toBe($largestBlock->id);
});

it('should cache largest block by transaction count', function () {
    $cache = new BlockCache();

    Block::factory()->create(['number_of_transactions' => 1]);

    $largestBlock = Block::factory()->create(['number_of_transactions' => 50]);

    Block::factory()->create(['number_of_transactions' => 3]);

    $this->artisan('explorer:cache-blocks');

    expect($cache->getLargestIdByTransactionCount())->toBe($largestBlock->id);
});
