<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Models\Wallet;

use App\Services\Transactions\TransactionDirectionIcon;
use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should determine if the transaction is sent', function () {
    $transaction = Transaction::factory()->create([
        'sender_public_key' => Wallet::factory()->create(['address' => 'sender'])->public_key,
        'recipient_id'      => Wallet::factory()->create(['address' => 'recipient'])->address,
    ]);

    expect((new TransactionDirectionIcon($transaction))->name('sender'))->toBe('sent');
});

it('should determine if the transaction is received', function () {
    $transaction = Transaction::factory()->create([
        'sender_public_key' => Wallet::factory()->create(['address' => 'sender'])->public_key,
        'recipient_id'      => Wallet::factory()->create(['address' => 'recipient'])->address,
    ]);

    expect((new TransactionDirectionIcon($transaction))->name('recipient'))->toBe('received');
});

it('should determine if the transaction is unknown', function () {
    $transaction = Transaction::factory()->create([
        'sender_public_key' => Wallet::factory()->create(['address' => 'sender'])->public_key,
        'recipient_id'      => Wallet::factory()->create(['address' => 'recipient'])->address,
    ]);

    expect((new TransactionDirectionIcon($transaction))->name('unknown'))->toBe('unknown');
});
