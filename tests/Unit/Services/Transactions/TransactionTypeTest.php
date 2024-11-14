<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Transactions\TransactionType;

it('should determine the type', function (string $type, string $expected) {
    $transaction     = Transaction::factory()->{$type}()->create();
    $transactionType = new TransactionType($transaction);

    expect($transactionType->{'is'.ucfirst($type)}())->toBeTrue();
    expect($transactionType->name())->toBe($expected);
})->with([
    [
        'transfer',
        'transfer',
    ],
    [
        'validatorRegistration',
        'validator-registration',
    ],
    [
        'vote',
        'vote',
    ],
    [
        'unvote',
        'unvote',
    ],
    [
        'multiSignature',
        'multi-signature',
    ],
    [
        'validatorResignation',
        'validator-resignation',
    ],
    [
        'multiPayment',
        'multi-payment',
    ],
]);

it('should determine is unknown type', function () {
    $transaction = Transaction::factory()->create([
        'type'  => 1234,
        'asset' => [],
    ]);
    $transactionType = new TransactionType($transaction);

    expect($transactionType->isUnknown())->toBeTrue();
    expect($transactionType->name())->toBe('unknown');
});

it('should play through every scenario of an unknown type', function (string $type) {
    $transaction = Transaction::factory()->{$type}()->create();

    expect((new TransactionType($transaction))->isUnknown())->toBeFalse();
})->with([
    ['transfer'],
    ['validatorRegistration'],
    ['vote'],
    ['unvote'],
    ['multiSignature'],
    ['validatorResignation'],
    ['multiPayment'],
]);
