<?php

declare(strict_types=1);

use App\Events\Statistics\TransactionDetails;
use App\Jobs\CacheBlocks;
use App\Models\Block;
use App\Models\Transaction;
use App\Services\Cache\BlockCache;
use Illuminate\Support\Facades\Event;

it('should cache largest block by amount', function () {
    Event::fake();

    $cache = new BlockCache();

    $largestBlock = Block::factory()->create(['amount' => 100 * 1e8]);

    (new CacheBlocks())->handle($cache);

    expect($cache->getLargestIdByAmount())->toBe($largestBlock->hash);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);
});

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

it('should trigger event if largest block by amount changes', function () {
    Event::fake();

    $cache = new BlockCache();

    $largestByAmount = Block::factory()->create([
        'amount'             => 1000 * 1e8,
        'fee'                => 1 * 1e8,
        'transactions_count' => 1,
    ]);

    $largestByFees = Block::factory()->create([
        'amount'             => 100 * 1e8,
        'fee'                => 100 * 1e8,
        'transactions_count' => 1,
    ]);

    $largestByTransactions = Block::factory()->create([
        'amount'             => 1 * 1e8,
        'fee'                => 1 * 1e8,
        'transactions_count' => 3,
    ]);

    (new CacheBlocks())->handle($cache);

    expect($cache->getLargestIdByAmount())->toBe($largestByAmount->hash);
    expect($cache->getLargestIdByFees())->toBe($largestByFees->hash);
    expect($cache->getLargestIdByTransactionCount())->toBe($largestByTransactions->hash);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);

    Event::fake();

    (new CacheBlocks())->handle($cache);

    Event::assertDispatchedTimes(TransactionDetails::class, 0);

    $updatedLargestByAmount = Block::factory()->create([
        'fee'                => 1 * 1e8,
        'transactions_count' => 1,
    ]);
    Transaction::factory()->create([
        'value'      => 10000 * 1e8,
        'block_hash' => $updatedLargestByAmount->hash,
    ]);

    (new CacheBlocks())->handle($cache);

    expect($cache->getLargestIdByAmount())->toBe($updatedLargestByAmount->hash);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);
});

it('should trigger event if largest block by fee changes', function () {
    Event::fake();

    $cache = new BlockCache();

    $largestByAmount = Block::factory()->create([
        'amount'             => 1000 * 1e8,
        'fee'                => 1 * 1e8,
        'transactions_count' => 1,
    ]);

    $largestByFees = Block::factory()->create([
        'amount'             => 100 * 1e8,
        'fee'                => 100 * 1e8,
        'transactions_count' => 1,
    ]);

    $largestByTransactions = Block::factory()->create([
        'amount'             => 1 * 1e8,
        'fee'                => 1 * 1e8,
        'transactions_count' => 3,
    ]);

    (new CacheBlocks())->handle($cache);

    expect($cache->getLargestIdByAmount())->toBe($largestByAmount->hash);
    expect($cache->getLargestIdByFees())->toBe($largestByFees->hash);
    expect($cache->getLargestIdByTransactionCount())->toBe($largestByTransactions->hash);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);

    Event::fake();

    (new CacheBlocks())->handle($cache);

    Event::assertNotDispatched(TransactionDetails::class);

    $updatedLargestByFees = Block::factory()->create([
        // same amount, but higher fee
        'amount'             => 1 * 1e8,
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

    $largestByAmount = Block::factory()->create([
        'amount'             => 1000 * 1e8,
        'fee'                => 1 * 1e8,
        'transactions_count' => 1,
    ]);

    $largestByFees = Block::factory()->create([
        'amount'             => 100 * 1e8,
        'fee'                => 100 * 1e8,
        'transactions_count' => 1,
    ]);

    $largestByTransactions = Block::factory()->create([
        'amount'             => 1 * 1e8,
        'fee'                => 1 * 1e8,
        'transactions_count' => 3,
    ]);

    (new CacheBlocks())->handle($cache);

    expect($cache->getLargestIdByAmount())->toBe($largestByAmount->hash);
    expect($cache->getLargestIdByFees())->toBe($largestByFees->hash);
    expect($cache->getLargestIdByTransactionCount())->toBe($largestByTransactions->hash);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);

    Event::fake();

    (new CacheBlocks())->handle($cache);

    Event::assertNotDispatched(TransactionDetails::class);

    $updatedLargestByTransactions = Block::factory()->create([
        // same amount, but more transactions
        'amount'             => 1 * 1e8,
        'fee'                => 1 * 1e8,
        'transactions_count' => 5,
    ]);

    (new CacheBlocks())->handle($cache);

    expect($cache->getLargestIdByTransactionCount())->toBe($updatedLargestByTransactions->hash);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);
});
