<?php

declare(strict_types=1);

use App\Services\Blockchain\NetworkFactory;
use Illuminate\Support\Facades\Http;

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
    Http::fake([
        'github.com' => [],
    ]);

    expect($this->subject->knownWallets())->toBeArray();
    expect($this->subject->knownWallets())->toHaveCount(0);
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
