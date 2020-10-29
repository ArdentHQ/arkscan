<?php

declare(strict_types=1);

use App\Aggregates\VotePercentageAggregate;
use App\Enums\CoreTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\Http;
use function Tests\configureExplorerDatabase;

beforeEach(function () {
    configureExplorerDatabase();

    Http::fakeSequence()->push([
        'data' => [
            'block' => [
                'height' => 5651290,
                'id'     => '7454506361e241a5c2c5d930fb059d28e3686a7aedc8058d9aac02f70aefe101',
            ],
            'supply' => '13628098200000000',
        ],
    ]);

    $wallet = Wallet::factory()->create(['balance' => '10000000000000000']);
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
    expect($this->subject->aggregate())->toBe('73.377809972047');
});
