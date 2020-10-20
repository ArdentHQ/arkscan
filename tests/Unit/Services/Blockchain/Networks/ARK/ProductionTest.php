<?php

declare(strict_types=1);

use App\Services\Blockchain\NetworkFactory;

use function Tests\fakeKnownWallets;

beforeEach(fn () => $this->subject = NetworkFactory::make('ark.production'));

it('should have a name', function () {
    expect($this->subject->name())->toBe('ARK Public Network');
});

it('should have an alias', function () {
    expect($this->subject->alias())->toBe('mainnet');
});

it('should have a currency name', function () {
    expect($this->subject->currency())->toBe('ARK');
});

it('should have a currency symbol', function () {
    expect($this->subject->currencySymbol())->toBe('Ѧ');
});

it('should have a required number of confirmations', function () {
    expect($this->subject->confirmations())->toBe(51);
});

it('should fetch known wallets', function () {
    fakeKnownWallets();

    expect($this->subject->knownWallets())->toBeArray();
    expect($this->subject->knownWallets())->toHaveCount(26);
});

it('should determine if the network currency can be exchanged', function () {
    expect($this->subject->canBeExchanged())->toBeTrue();
});

it('should have a host', function () {
    expect($this->subject->host())->toBe('https://wallets.ark.io/api');
});
