<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Transaction;
use App\Services\Cache\BlockCache;
use Illuminate\Support\Facades\Event;

it('should run job', function () {
    Event::fake();

    $cache = new BlockCache();

    Transaction::factory()->create(['value' => 0]);

    $largestBlock = Block::factory()->create([
        'amount' => 1000 * 1e18,
    ]);

    Block::factory()->create([
        'amount' => 0,
    ]);

    expect($cache->getLargestIdByAmount())->toBeNull();

    $this->artisan('explorer:cache-blocks');

    expect($cache->getLargestIdByAmount())->toBe($largestBlock->hash);
});

it('should cache largest block by fee', function () {
    $cache = new BlockCache();

    Block::factory()->create(['fee' => 0]);

    $largestBlock = Block::factory()->create(['fee' => 100 * 1e18]);

    Block::factory()->create(['fee' => 0]);

    $this->artisan('explorer:cache-blocks');

    expect($cache->getLargestIdByFees())->toBe($largestBlock->hash);
});

it('should cache largest block by transaction count', function () {
    $cache = new BlockCache();

    Block::factory()->create(['transactions_count' => 1]);

    $largestBlock = Block::factory()->create(['transactions_count' => 50]);

    Block::factory()->create(['transactions_count' => 3]);

    $this->artisan('explorer:cache-blocks');

    expect($cache->getLargestIdByTransactionCount())->toBe($largestBlock->hash);
});
