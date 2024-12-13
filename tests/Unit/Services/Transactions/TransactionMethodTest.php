<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Transactions\TransactionMethod;

it('should determine the type', function (string $type, string $expected) {
    $transaction       = Transaction::factory()->{$type}()->create();
    $transactionMethod = new TransactionMethod($transaction);

    expect($transactionMethod->{'is'.ucfirst($type)}())->toBeTrue();
    expect($transactionMethod->name())->toBe($expected);
})->with([
    [
        'transfer',
        'Transfer',
    ],
    [
        'validatorRegistration',
        'Registration',
    ],
    [
        'unvote',
        'Unvote',
    ],
    [
        'validatorResignation',
        'Resignation',
    ],
    [
        'usernameRegistration',
        'Username Registration',
    ],
    [
        'usernameResignation',
        'Username Resignation',
    ],
]);

it('should determine the type with vote', function () {
    $validator    = Wallet::factory()->activeValidator()->create();
    $transaction       = Transaction::factory()->vote($validator->address)->create();
    $transactionMethod = new TransactionMethod($transaction);

    expect($transactionMethod->isVote())->toBeTrue();
    expect($transactionMethod->name())->toBe('Vote');
});