<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Models\Wallet;

use App\Services\Transactions\TransactionDirection;
use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should determine if the transaction is sent', function () {
    $transaction = Transaction::factory()->create([
        'sender_public_key' => Wallet::factory()->create(['address' => 'sender'])->public_key,
        'recipient_id'      => Wallet::factory()->create(['address' => 'recipient'])->address,
    ]);

    expect((new TransactionDirection($transaction))->isSent('sender'))->toBeTrue();
    expect((new TransactionDirection($transaction))->isSent('recipient'))->toBeFalse();
});

it('should determine if the transaction is received', function () {
    $transaction = Transaction::factory()->create([
        'sender_public_key' => Wallet::factory()->create(['address' => 'sender'])->public_key,
        'recipient_id'      => Wallet::factory()->create(['address' => 'recipient'])->address,
    ]);

    expect((new TransactionDirection($transaction))->isReceived('recipient'))->toBeTrue();
    expect((new TransactionDirection($transaction))->isReceived('sender'))->toBeFalse();
});
