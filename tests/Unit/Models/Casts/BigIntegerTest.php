<?php

declare(strict_types=1);

use App\Models\Casts\BigInteger;
use App\Models\Wallet;
use App\Services\BigNumber;

it('should convert integer', function () {
    $wallet = Wallet::factory()->create([
        'balance' => BigNumber::new(1000),
    ]);

    $value = (new BigInteger())->get($wallet, 'balance', BigNumber::new(1000), []);

    expect($value)->toEqual(BigNumber::new(1000));
});

it('should convert bignumber', function () {
    $wallet = Wallet::factory()->create([
        'balance' => '1000',
    ]);

    $value = (new BigInteger())->get($wallet, 'balance', '1000', []);

    expect($value)->toEqual(BigNumber::new(1000));
});
