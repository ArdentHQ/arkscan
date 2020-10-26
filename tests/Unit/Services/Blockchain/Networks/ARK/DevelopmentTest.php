<?php

declare(strict_types=1);

use App\Services\Blockchain\NetworkFactory;
use BitWasp\Bitcoin\Network\Network;
use Carbon\Carbon;
use function Tests\fakeKnownWallets;

beforeEach(fn () => $this->subject = NetworkFactory::make('ark.development'));

it('should have a name', function () {
    expect($this->subject->name())->toBe('ARK Development Network');
});

it('should have an alias', function () {
    expect($this->subject->alias())->toBe('devnet');
});

it('should have a currency name', function () {
    expect($this->subject->currency())->toBe('DARK');
});

it('should have a currency symbol', function () {
    expect($this->subject->currencySymbol())->toBe('DѦ');
});

it('should have a required number of confirmations', function () {
    expect($this->subject->confirmations())->toBe(51);
});

it('should fetch known wallets', function () {
    fakeKnownWallets();

    expect($this->subject->knownWallets())->toBeArray();
});

it('should determine if the network currency can be exchanged', function () {
    expect($this->subject->canBeExchanged())->toBeFalse();
});

it('should have a host', function () {
    expect($this->subject->host())->toBe('https://dwallets.ark.io/api');
});

it('should determine if the network is on marketsquare', function () {
    expect($this->subject->usesMarketsquare())->toBeFalse();
});

it('should have an epoch', function () {
    expect($this->subject->epoch())->toBeInstanceOf(Carbon::class);
});

it('should have a delegate count', function () {
    expect($this->subject->delegateCount())->toBeInt();
});

it('should have a block time', function () {
    expect($this->subject->blockTime())->toBeInt();
});

it('should have a block reward', function () {
    expect($this->subject->blockReward())->toBeInt();
});

it('should have a config', function () {
    expect($this->subject->config())->toBeInstanceOf(Network::class);
});
