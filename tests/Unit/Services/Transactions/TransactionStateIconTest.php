<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;

use App\Services\Transactions\TransactionStateIcon;
use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should determine if the transaction is confirmed', function (int $transactionHeight, int $blockHeight, string $icon) {
    Block::factory()->create(['height' => $blockHeight]);

    $transaction = Transaction::factory()->create([
        'block_id'          => Block::factory()->create(['height' => $transactionHeight])->id,
        'sender_public_key' => Wallet::factory()->create(['address' => 'sender'])->public_key,
        'recipient_id'      => Wallet::factory()->create(['address' => 'recipient'])->address,
    ]);

    expect((new TransactionStateIcon($transaction))->name())->toBe($icon);
})->with([
    [1, 100, 'confirmed'],
    [1, 2, 'unknown'],
]);
