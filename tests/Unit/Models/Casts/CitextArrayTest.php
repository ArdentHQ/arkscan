<?php

declare(strict_types=1);

use App\Models\Casts\CitextArray;
use App\Models\Transaction;

it('should convert from string to array', function () {
    $transaction = Transaction::factory()->create();

    $value = (new CitextArray())->get($transaction, 'multi_payment_recipients', '{123,456}', []);

    expect($value)->toEqual(['123', '456']);
});

it('should not convert to array if already array', function () {
    $transaction = Transaction::factory()->create();

    $value = (new CitextArray())->get($transaction, 'multi_payment_recipients', ['123', '456'], []);

    expect($value)->toEqual(['123', '456']);
});

it('should convert to string from array', function () {
    $transaction = Transaction::factory()->create();

    $value = (new CitextArray())->set($transaction, 'multi_payment_recipients', ['123', '456'], []);

    expect($value)->toEqual('{123,456}');
});

it('should not convert to string if already string', function () {
    $transaction = Transaction::factory()->create();

    $value = (new CitextArray())->set($transaction, 'multi_payment_recipients', '{123,456}', []);

    expect($value)->toEqual('{123,456}');
});
