<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Transactions\TransactionTypeSlug;

it('should determine the type', function (string $type, string $expectedExact, ?string $expectedGeneric = null) {
    $transaction     = Transaction::factory()->{$type}()->create();
    $transactionType = new TransactionTypeSlug($transaction);

    expect($transactionType->exact())->toBe($expectedExact);

    if ($expectedGeneric) {
        expect($transactionType->generic())->toBe($expectedGeneric);
    }
})->with([
    [
        'transfer',
        'transfer',
        'transfer',
    ],
    [
        'validatorRegistration',
        'validator-registration',
        'validator-registration',
    ],
    [
        'vote',
        'vote',
        'vote',
    ],
    [
        'unvote',
        'unvote',
        'unvote',
    ],
    [
        'voteCombination',
        'vote-combination',
        'vote-combination',
    ],
    [
        'multiSignature',
        'multi-signature',
        'multi-signature',
    ],
    [
        'validatorResignation',
        'validator-resignation',
        'validator-resignation',
    ],
    [
        'multiPayment',
        'multi-payment',
        'multi-payment',
    ],
    [
        'usernameRegistration',
        'username-registration',
        'username-registration',
    ],
    [
        'usernameResignation',
        'username-resignation',
        'username-resignation',
    ],
]);

it('should determine is unknown type', function () {
    $transaction = Transaction::factory()->create([
        'type'       => 0,
        'type_group' => 0,
        'asset'      => [],
    ]);
    $transactionType = new TransactionTypeSlug($transaction);

    expect($transactionType->generic())->toBe('unknown');
    expect($transactionType->exact())->toBe('unknown');
});
