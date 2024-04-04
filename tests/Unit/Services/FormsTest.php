<?php

declare(strict_types=1);

use App\Services\Forms;
use Illuminate\Support\Facades\Config;

it('it should get all transaction options', function () {
    expect(Forms::getTransactionOptions())->toHaveKeys([
        'all',
        'transfer',
        'secondSignature',
        'validatorRegistration',
        'vote',
        'voteCombination',
        'multiSignature',
        'ipfs',
        'multiPayment',
        'magistrate',
    ]);
});
