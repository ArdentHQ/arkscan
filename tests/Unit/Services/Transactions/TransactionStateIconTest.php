<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;

use App\Services\Transactions\TransactionStateIcon;
use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should determine if the transaction is confirmed', function () {
    Block::factory()->create(['height' => 2000]);

    $transaction = Transaction::factory()->create([
        'block_id'          => Block::factory()->create(['height' => 1000])->id,
        'sender_public_key' => Wallet::factory()->create(['address' => 'sender'])->public_key,
        'recipient_id'      => Wallet::factory()->create(['address' => 'recipient'])->address,
    ]);

    expect((new TransactionStateIcon($transaction))->name('sender'))->toBe('confirmed');
});
