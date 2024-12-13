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

    $largestBlock = Block::factory()->create(['total_amount' => 100 * 1e8]);

    (new CacheBlocks())->handle($cache);

    expect($cache->getLargestIdByAmount())->toBe($largestBlock->id);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);
});

it('should cache largest block by fee', function () {
    Event::fake();

    $cache = new BlockCache();

    Block::factory()->create(['total_fee' => 0]);

    $largestBlock = Block::factory()->create(['total_fee' => 100 * 1e8]);

    Block::factory()->create(['total_fee' => 0]);

    (new CacheBlocks())->handle($cache);

    expect($cache->getLargestIdByFees())->toBe($largestBlock->id);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);
});

it('should cache largest block by transaction count', function () {
    Event::fake();

    $cache = new BlockCache();

    Block::factory()->create(['number_of_transactions' => 1]);

    $largestBlock = Block::factory()->create(['number_of_transactions' => 50]);

    Block::factory()->create(['number_of_transactions' => 3]);

    (new CacheBlocks())->handle($cache);

    expect($cache->getLargestIdByTransactionCount())->toBe($largestBlock->id);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);
});

it('should not trigger event if no change', function () {
    Event::fake();

    $cache = new BlockCache();

    Block::factory()->create(['number_of_transactions' => 1]);

    $largestBlock = Block::factory()->create(['number_of_transactions' => 50]);

    Block::factory()->create(['number_of_transactions' => 3]);

    (new CacheBlocks())->handle($cache);

    expect($cache->getLargestIdByTransactionCount())->toBe($largestBlock->id);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);

    Event::fake();

    (new CacheBlocks())->handle($cache);

    Event::assertDispatchedTimes(TransactionDetails::class, 0);
});

it('should trigger event if largest block by amount changes', function () {
    Event::fake();

    $cache = new BlockCache();

    $largestByAmount = Block::factory()->create([
        'total_amount'           => 1000 * 1e8,
        'total_fee'              => 1 * 1e8,
        'number_of_transactions' => 1,
    ]);

    $largestByFees = Block::factory()->create([
        'total_amount'           => 100 * 1e8,
        'total_fee'              => 100 * 1e8,
        'number_of_transactions' => 1,
    ]);

    $largestByTransactions = Block::factory()->create([
        'total_amount'           => 1 * 1e8,
        'total_fee'              => 1 * 1e8,
        'number_of_transactions' => 3,
    ]);

    (new CacheBlocks())->handle($cache);

    expect($cache->getLargestIdByAmount())->toBe($largestByAmount->id);
    expect($cache->getLargestIdByFees())->toBe($largestByFees->id);
    expect($cache->getLargestIdByTransactionCount())->toBe($largestByTransactions->id);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);

    Event::fake();

    (new CacheBlocks())->handle($cache);

    Event::assertDispatchedTimes(TransactionDetails::class, 0);

    $updatedLargestByAmount = Block::factory()->create([
        'total_fee'              => 1 * 1e8,
        'number_of_transactions' => 1,
    ]);
    Transaction::factory()->create([
        'amount'   => 10000 * 1e8,
        'block_id' => $updatedLargestByAmount->id,
    ]);

    (new CacheBlocks())->handle($cache);

    expect($cache->getLargestIdByAmount())->toBe($updatedLargestByAmount->id);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);
});

it('should trigger event if largest block by fee changes', function () {
    Event::fake();

    $cache = new BlockCache();

    $largestByAmount = Block::factory()->create([
        'total_amount'           => 1000 * 1e8,
        'total_fee'              => 1 * 1e8,
        'number_of_transactions' => 1,
    ]);

    $largestByFees = Block::factory()->create([
        'total_amount'           => 100 * 1e8,
        'total_fee'              => 100 * 1e8,
        'number_of_transactions' => 1,
    ]);

    $largestByTransactions = Block::factory()->create([
        'total_amount'           => 1 * 1e8,
        'total_fee'              => 1 * 1e8,
        'number_of_transactions' => 3,
    ]);

    (new CacheBlocks())->handle($cache);

    expect($cache->getLargestIdByAmount())->toBe($largestByAmount->id);
    expect($cache->getLargestIdByFees())->toBe($largestByFees->id);
    expect($cache->getLargestIdByTransactionCount())->toBe($largestByTransactions->id);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);

    Event::fake();

    (new CacheBlocks())->handle($cache);

    Event::assertDispatchedTimes(TransactionDetails::class, 0);

    $updatedLargestByFees = Block::factory()->create([
        'total_fee'              => 1000 * 1e8,
        'number_of_transactions' => 1,
    ]);

    (new CacheBlocks())->handle($cache);

    expect($cache->getLargestIdByFees())->toBe($updatedLargestByFees->id);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);
});

it('should trigger event if largest block by transaction count changes', function () {
    Event::fake();

    $cache = new BlockCache();

    $largestByAmount = Block::factory()->create([
        'total_amount'           => 1000 * 1e8,
        'total_fee'              => 1 * 1e8,
        'number_of_transactions' => 1,
    ]);

    $largestByFees = Block::factory()->create([
        'total_amount'           => 100 * 1e8,
        'total_fee'              => 100 * 1e8,
        'number_of_transactions' => 1,
    ]);

    $largestByTransactions = Block::factory()->create([
        'total_amount'           => 1 * 1e8,
        'total_fee'              => 1 * 1e8,
        'number_of_transactions' => 3,
    ]);

    (new CacheBlocks())->handle($cache);

    expect($cache->getLargestIdByAmount())->toBe($largestByAmount->id);
    expect($cache->getLargestIdByFees())->toBe($largestByFees->id);
    expect($cache->getLargestIdByTransactionCount())->toBe($largestByTransactions->id);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);

    Event::fake();

    (new CacheBlocks())->handle($cache);

    Event::assertDispatchedTimes(TransactionDetails::class, 0);

    $updatedLargestByTransactions = Block::factory()->create([
        'total_fee'              => 1 * 1e8,
        'number_of_transactions' => 5,
    ]);

    (new CacheBlocks())->handle($cache);

    expect($cache->getLargestIdByTransactionCount())->toBe($updatedLargestByTransactions->id);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);
});
