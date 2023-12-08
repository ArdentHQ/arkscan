<?php

declare(strict_types=1);

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
        'amount' => 3000 * 1e8,
        'fee'    => 10 * 1e8,
    ]);

    expect((new LargestTransactionAggregate())->aggregate()->id)->toBe($transaction->id);
});

it('should get largest transaction including multipayments', function () {
    Transaction::factory()->transfer()->create([
        'amount' => 2000 * 1e8,
        'fee'    => 10 * 1e8,
    ]);
    Transaction::factory()->transfer()->create([
        'amount' => 6000 * 1e8,
        'fee'    => 10 * 1e8,
    ]);
    $transaction = Transaction::factory()->multipayment()->create([
        'amount' => 0,
        'fee'    => 10 * 1e8,
        'asset'  => [
            'payments' => [
                [
                    'recipientId' => 'test-address',
                    'amount'      => 300 * 1e8,
                ],
                [
                    'recipientId' => 'test-address',
                    'amount'      => 300 * 1e8,
                ],
                [
                    'recipientId' => 'test-address',
                    'amount'      => 5000 * 1e8,
                ],
                [
                    'recipientId' => 'test-address',
                    'amount'      => 300 * 1e8,
                ],
                [
                    'recipientId' => 'test-address',
                    'amount'      => 300 * 1e8,
                ],
            ],
        ],
    ]);

    expect((new LargestTransactionAggregate())->aggregate()->id)->toBe($transaction->id);
});

it('should return null if no records', function () {
    expect((new LargestTransactionAggregate())->aggregate())->toBeNull();
});
