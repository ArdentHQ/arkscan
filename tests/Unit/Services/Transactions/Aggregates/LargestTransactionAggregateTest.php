<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Transactions\Aggregates\LargestTransactionAggregate;

it('should get largest transaction', function () {
    Transaction::factory()->transfer()->create([
        'amount'    => 2000 * 1e18,
        'gas_price' => 10 * 1e18,
    ]);
    $transaction = Transaction::factory()->transfer()->create([
        'amount'    => 6000 * 1e18,
        'gas_price' => 10 * 1e18,
    ]);
    Transaction::factory()->transfer()->create([
        'amount'    => 3000 * 1e18,
        'gas_price' => 10 * 1e18,
    ]);

    expect((new LargestTransactionAggregate())->aggregate()->hash)->toBe($transaction->hash);
});

it('should return null if no records', function () {
    expect((new LargestTransactionAggregate())->aggregate())->toBeNull();
});
