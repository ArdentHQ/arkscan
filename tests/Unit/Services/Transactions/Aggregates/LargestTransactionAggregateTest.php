<?php

use App\Models\Transaction;
use App\Services\Transactions\Aggregates\LargestTransactionAggregate;

it('should get largest transaction', function () {
    Transaction::factory()->transfer()->create([
        'amount' => 2000 * 1e8,
        'fee'    => 10 * 1e8,
    ]);
    $transaction = Transaction::factory()->transfer()->create([
        'amount' => 6000 * 1e8,
        'fee'    => 10 * 1e8,
    ]);
    Transaction::factory()->transfer()->create([
        'amount' => 2000 * 1e8,
        'fee'    => 10 * 1e8,
    ]);

    expect((new LargestTransactionAggregate())->aggregate()->id)->toBe($transaction->id);
});

it('should return null if no records', function () {
    expect((new LargestTransactionAggregate())->aggregate())->toBeNull();
});
