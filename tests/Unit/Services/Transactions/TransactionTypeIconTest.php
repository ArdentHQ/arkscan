<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Transactions\TransactionTypeIcon;

it('should determine the icon that matches the type', function (string $type, string $icon) {
    $transaction = Transaction::factory()->{$type}()->create();

    expect((new TransactionTypeIcon($transaction))->name())->toBe($icon);
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
        'voteCombination',
        'vote-combination',
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
    [
        'usernameRegistration',
        'username-registration',
    ],
    [
        'usernameResignation',
        'username-resignation',
    ],
]);

it('should determine the icon of unknown type', function () {
    $transaction = Transaction::factory()->create([
        'type'       => 0,
        'type_group' => 0,
    ]);

    expect((new TransactionTypeIcon($transaction))->name())->toBe('unknown');
});
