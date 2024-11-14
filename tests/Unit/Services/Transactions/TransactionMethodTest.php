<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Transactions\TransactionMethod;

it('should determine the type', function (string $type, string $expected) {
    $transaction     = Transaction::factory()->{$type}()->create();
    $transactionMethod = new TransactionMethod($transaction);

    expect($transactionMethod->{'is'.ucfirst($type)}())->toBeTrue();
    expect($transactionMethod->name())->toBe($expected);
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
        'validatorResignation',
        'validator-resignation',
    ],
]);
