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
    expect((new TransactionDirection($transaction))->isSent('unknown'))->toBeFalse();
});

it('should determine if the transaction is sent is missing', function () {
    $transaction = Transaction::factory()->create([
        'sender_public_key' => 'unknown',
        'recipient_id'      => Wallet::factory()->create(['address' => 'recipient'])->address,
    ]);

    expect((new TransactionDirection($transaction))->isSent('recipient'))->toBeFalse();
});

it('should determine if the transaction is received', function () {
    $transaction = Transaction::factory()->create([
        'sender_public_key' => Wallet::factory()->create(['address' => 'sender'])->public_key,
        'recipient_id'      => Wallet::factory()->create(['address' => 'recipient'])->address,
    ]);

    expect((new TransactionDirection($transaction))->isReceived('recipient'))->toBeTrue();
    expect((new TransactionDirection($transaction))->isReceived('sender'))->toBeFalse();
    expect((new TransactionDirection($transaction))->isReceived('unknown'))->toBeFalse();
});

it('should determine if the transaction is received if the recipient is missing', function () {
    $transaction = Transaction::factory()->create([
        'sender_public_key' => Wallet::factory()->create(['address' => 'sender'])->public_key,
        'recipient_id'      => 'unknown',
    ]);

    expect((new TransactionDirection($transaction))->isReceived('unknown'))->toBeFalse();
});
