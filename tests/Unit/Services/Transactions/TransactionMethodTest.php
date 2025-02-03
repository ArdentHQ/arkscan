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
        'unvote',
        'Unvote',
    ],
    [
        'validatorRegistration',
        'Validator Registration',
    ],
    [
        'validatorResignation',
        'Validator Resignation',
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
    $validator         = Wallet::factory()->activeValidator()->create();
    $transaction       = Transaction::factory()->vote($validator->address)->create();
    $transactionMethod = new TransactionMethod($transaction);

    expect($transactionMethod->isVote())->toBeTrue();
    expect($transactionMethod->name())->toBe('Vote');
});

it('should determine the name from the contracts if unhandled type', function () {
    $transaction       = Transaction::factory()
        // getRounds, there is no isGetRounds method
        ->withPayload('40f74f470000000000000000000000000000000000000000000000000000000000000001000000000000000000000000000000000000000000000000000000000000000a')
        ->create();

    $transactionMethod = new TransactionMethod($transaction);

    expect($transactionMethod->name())->toBe('getRounds');
});
