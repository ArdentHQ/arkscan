<?php

declare(strict_types=1);

use App\Services\Forms;
use Illuminate\Support\Facades\Config;

it('it should get all transaction options if timelock is enabled', function () {
    Config::set('explorer.networks.development.hasTimelock', true);

    expect(Forms::getTransactionOptions())->toHaveKeys([
        'all',
        'transfer',
        'secondSignature',
        'delegateRegistration',
        'vote',
        'voteCombination',
        'multiSignature',
        'ipfs',
        'multiPayment',
        'timelock',
        'timelockClaim',
        'timelockRefund',
        'magistrate',
    ]);
});

it('it should exclude timelock transaction options if timelock is disabled', function () {
    Config::set('explorer.networks.development.hasTimelock', false);

    expect(Forms::getTransactionOptions())->toHaveKeys([
        'all',
        'transfer',
        'secondSignature',
        'delegateRegistration',
        'vote',
        'voteCombination',
        'multiSignature',
        'ipfs',
        'multiPayment',
        'magistrate',
    ]);
});
