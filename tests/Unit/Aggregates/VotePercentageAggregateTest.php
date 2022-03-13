<?php

declare(strict_types=1);

use App\Aggregates\VotePercentageAggregate;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;

beforeEach(function () {
    (new NetworkCache())->setSupply(fn () => '13628098200000000');

    $wallet = Wallet::factory()->create(['balance' => '10000000000000000']);
    $block  = Block::factory()->create(['generator_public_key' => $wallet->public_key]);

    Transaction::factory()->vote()->create([
        'block_id'          => $block->id,
        'sender_public_key' => $wallet->public_key,
        'recipient_id'      => $wallet->address,
    ]);

    $this->subject = new VotePercentageAggregate();
});

it('should aggregate and format', function () {
    expect($this->subject->aggregate())->toBeString();
});
