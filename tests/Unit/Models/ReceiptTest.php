<?php

declare(strict_types=1);

use App\Models\Receipt;
use App\Models\Transaction;

it('should link to a transaction', function () {
    $transaction = Transaction::factory()->create();
    $receipt     = Receipt::factory()->create(['id' => $transaction->id]);

    expect($receipt->transaction->id)->toBe($transaction->id);
});

it('should link to a transaction through factory', function () {
    $receipt     = Receipt::factory()->withTransaction()->create();
    $transaction = Transaction::first();

    expect($receipt->transaction->id)->toBe($transaction->id);
});
