<?php

declare(strict_types=1);

use App\Aggregates\VotePercentageAggregate;
use App\Enums\CoreTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use function Tests\configureExplorerDatabase;

beforeEach(function () {
    configureExplorerDatabase();

    $wallet = Wallet::factory()->create(['balance' => 100000000]);
    $block = Block::factory()->create(['generator_public_key' => $wallet->public_key]);

    Transaction::factory()->create([
        'block_id'          => $block->id,
        'type'              => CoreTransactionTypeEnum::VOTE,
        'type_group'        => TransactionTypeGroupEnum::CORE,
        'sender_public_key' => $wallet->public_key,
        'recipient_id'      => $wallet->address,
    ]);

    $this->subject = new VotePercentageAggregate();
});

it('should aggregate and format', function () {
    expect($this->subject->aggregate())->toBe('65.437');
});
