<?php

declare(strict_types=1);

use App\Models\Receipt;
use App\Models\Transaction;

it('should link to a transaction', function () {
    $transaction = Transaction::factory()->create();
    $receipt     = Receipt::factory()->create(['transaction_hash' => $transaction->hash]);

    expect($receipt->transaction->hash)->toBe($transaction->hash);
});

it('should link to a transaction through factory', function () {
    $receipt     = Receipt::factory()->withTransaction()->create();
    $transaction = Transaction::first();

    expect($receipt->transaction->hash)->toBe($transaction->hash);
});
