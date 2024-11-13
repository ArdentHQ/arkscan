<?php

declare(strict_types=1);

use App\Services\Forms;

it('should get all transaction options', function () {
    expect(Forms::getTransactionOptions())->toHaveKeys([
        'all',
        'transfer',
        'validatorRegistration',
        'validatorResignation',
        'vote',
        'multiSignature',
        'multiPayment',
        'usernameRegistration',
        'usernameResignation',
    ]);
});
