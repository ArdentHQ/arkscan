<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Transactions\TransactionDirectionIcon;

it('should determine if the transaction is sent', function () {
    $sender      = Wallet::factory()->create();
    $transaction = Transaction::factory()->create([
        'sender_public_key' => $sender->public_key,
        'recipient_id'      => Wallet::factory()->create()->address,
    ]);

    expect((new TransactionDirectionIcon($transaction))->name($sender->address))->toBe('sent');
});

it('should determine if the transaction is received', function () {
    $transaction = Transaction::factory()->create([
        'sender_public_key' => Wallet::factory()->create()->public_key,
        'recipient_id'      => $recipient = Wallet::factory()->create()->address,
    ]);

    expect((new TransactionDirectionIcon($transaction))->name($recipient))->toBe('received');
});

it('should determine if the transaction is unknown', function () {
    $transaction = Transaction::factory()->create();

    expect((new TransactionDirectionIcon($transaction))->name('unknown'))->toBe('unknown');
});
