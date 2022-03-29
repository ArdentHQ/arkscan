<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Transactions\TransactionDirection;

it('should determine if the transaction is sent', function () {
    $sender      = Wallet::factory()->create();
    $transaction = Transaction::factory()->create([
        'sender_public_key' => $sender->public_key,
        'recipient_id'      => Wallet::factory()->create(['address' => 'recipient'])->address,
    ]);

    expect((new TransactionDirection($transaction))->isSent($sender->address))->toBeTrue();
    expect((new TransactionDirection($transaction))->isSent('recipient'))->toBeFalse();
    expect((new TransactionDirection($transaction))->isSent('unknown'))->toBeFalse();
});

it('should determine if the transaction is sent is missing', function () {
    $transaction = Transaction::factory()->create([
        'recipient_id' => Wallet::factory()->create(['address' => 'recipient'])->address,
    ]);

    expect((new TransactionDirection($transaction))->isSent('recipient'))->toBeFalse();
});

it('should determine if the transaction is received', function () {
    $transaction = Transaction::factory()->create([
        'sender_public_key' => Wallet::factory()->create()->public_key,
        'recipient_id'      => Wallet::factory()->create(['address' => 'recipient'])->address,
    ]);

    expect((new TransactionDirection($transaction))->isReceived('recipient'))->toBeTrue();
    expect((new TransactionDirection($transaction))->isReceived('D6Z26L69gdk9qYmTv5uzk3uGepigtHY4ax'))->toBeFalse();
    expect((new TransactionDirection($transaction))->isReceived('unknown'))->toBeFalse();
});

it('should determine if the transaction is received if the recipient is missing', function () {
    $transaction = Transaction::factory()->create([
        'sender_public_key' => Wallet::factory()->create()->public_key,
        'recipient_id'      => null,
    ]);

    expect((new TransactionDirection($transaction))->isReceived('unknown'))->toBeFalse();
});
