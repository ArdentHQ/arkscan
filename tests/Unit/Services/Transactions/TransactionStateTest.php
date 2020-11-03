<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Models\Wallet;

use App\Services\Cache\NetworkCache;
use App\Services\Transactions\TransactionState;
use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should determine if the transaction is confirmed', function () {
    (new NetworkCache())->setHeight(2000);

    $transaction = Transaction::factory()->create([
        'block_height'      => 1000,
        'sender_public_key' => Wallet::factory()->create(['address' => 'sender'])->public_key,
        'recipient_id'      => Wallet::factory()->create(['address' => 'recipient'])->address,
    ]);

    expect((new TransactionState($transaction))->isConfirmed())->toBeTrue();
});

it('should determine if the transaction is not confirmed', function () {
    $transaction = Transaction::factory()->create([
        'sender_public_key' => Wallet::factory()->create(['address' => 'sender'])->public_key,
        'recipient_id'      => Wallet::factory()->create(['address' => 'recipient'])->address,
    ]);

    expect((new TransactionState($transaction))->isConfirmed())->toBeFalse();
});

it('should determine if the transaction is not confirmed if the block is missing', function () {
    $transaction = Transaction::factory()->create([
        'block_id' => 'unknown',
    ]);

    expect((new TransactionState($transaction))->isConfirmed())->toBeFalse();
});
