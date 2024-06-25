<?php

declare(strict_types=1);

use App\Events\Statistics\TransactionDetails;
use App\Models\Block;
use App\Models\Transaction;
use App\Services\Cache\BlockCache;
use Illuminate\Support\Facades\Event;

it('should run job', function () {
    Event::fake();

    $cache = new BlockCache();

    Transaction::factory()->create(['amount' => 0]);

    $largestBlock = Block::factory()->create();
    Transaction::factory()->create([
        'amount'   => 1000 * 1e8,
        'block_id' => $largestBlock->id,
    ]);

    expect($cache->getLargestIdByAmount())->toBeNull();

    $this->artisan('explorer:cache-blocks');

    expect($cache->getLargestIdByAmount())->toBe($largestBlock->id);
});
