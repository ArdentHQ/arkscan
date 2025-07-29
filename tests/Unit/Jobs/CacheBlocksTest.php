<?php

declare(strict_types=1);

use App\Events\Statistics\TransactionDetails;
use App\Jobs\CacheBlocks;
use App\Models\Block;
use App\Services\Cache\BlockCache;
use Illuminate\Support\Facades\Event;

it('should cache largest block by fee', function () {
    Event::fake();

    $cache = new BlockCache();

    Block::factory()->create(['fee' => 0]);

    $largestBlock = Block::factory()->create(['fee' => 100 * 1e8]);

    Block::factory()->create(['fee' => 0]);

    (new CacheBlocks())->handle($cache);

    expect($cache->getLargestIdByFees())->toBe($largestBlock->hash);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);
});

it('should cache largest block by transaction count', function () {
    Event::fake();

    $cache = new BlockCache();

    Block::factory()->create(['transactions_count' => 1]);

    $largestBlock = Block::factory()->create(['transactions_count' => 50]);

    Block::factory()->create(['transactions_count' => 3]);

    (new CacheBlocks())->handle($cache);

    expect($cache->getLargestIdByTransactionCount())->toBe($largestBlock->hash);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);
});

it('should not trigger event if no change', function () {
    Event::fake();

    $cache = new BlockCache();

    Block::factory()->create(['transactions_count' => 1]);

    $largestBlock = Block::factory()->create(['transactions_count' => 50]);

    Block::factory()->create(['transactions_count' => 3]);

    (new CacheBlocks())->handle($cache);

    expect($cache->getLargestIdByTransactionCount())->toBe($largestBlock->hash);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);

    Event::fake();

    (new CacheBlocks())->handle($cache);

    Event::assertNotDispatched(TransactionDetails::class);
});

it('should trigger event if largest block by fee changes', function () {
    Event::fake();

    $cache = new BlockCache();

    Block::factory()->create([
        'fee'                => 10 * 1e8,
        'transactions_count' => 1,
    ]);

    $largestByFees = Block::factory()->create([
        'fee'                => 100 * 1e8,
        'transactions_count' => 2,
    ]);

    $largestByTransactions = Block::factory()->create([
        'fee'                => 1 * 1e8,
        'transactions_count' => 3,
    ]);

    (new CacheBlocks())->handle($cache);

    expect($cache->getLargestIdByFees())->toBe($largestByFees->hash);
    expect($cache->getLargestIdByTransactionCount())->toBe($largestByTransactions->hash);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);

    Event::fake();

    (new CacheBlocks())->handle($cache);

    Event::assertNotDispatched(TransactionDetails::class);

    // same amount, but higher fee
    $updatedLargestByFees = Block::factory()->create([
        'fee'                => 1000 * 1e8,
        'transactions_count' => 1,
    ]);

    (new CacheBlocks())->handle($cache);

    expect($cache->getLargestIdByFees())->toBe($updatedLargestByFees->hash);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);
});

it('should trigger event if largest block by transaction count changes', function () {
    Event::fake();

    $cache = new BlockCache();

    Block::factory()->create([
        'fee'                => 10 * 1e8,
        'transactions_count' => 1,
    ]);

    $largestByFees = Block::factory()->create([
        'fee'                => 100 * 1e8,
        'transactions_count' => 2,
    ]);

    $largestByTransactions = Block::factory()->create([
        'fee'                => 1 * 1e8,
        'transactions_count' => 3,
    ]);

    (new CacheBlocks())->handle($cache);

    expect($cache->getLargestIdByFees())->toBe($largestByFees->hash);
    expect($cache->getLargestIdByTransactionCount())->toBe($largestByTransactions->hash);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);

    Event::fake();

    (new CacheBlocks())->handle($cache);

    Event::assertNotDispatched(TransactionDetails::class);

    // same amount, but more transactions
    $updatedLargestByTransactions = Block::factory()->create([
        'fee'                => 1 * 1e8,
        'transactions_count' => 5,
    ]);

    (new CacheBlocks())->handle($cache);

    expect($cache->getLargestIdByTransactionCount())->toBe($updatedLargestByTransactions->hash);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);
});
