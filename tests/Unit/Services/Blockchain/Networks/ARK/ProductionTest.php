<?php

declare(strict_types=1);

use App\Services\Blockchain\NetworkFactory;
use Illuminate\Support\Facades\Http;

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
    Http::fake([
        'github.com' => [
            'AagJoLEnpXYkxYdYkmdDSNMLjjBkLJ6T67' => 'ACF Hot Wallet',
            'AWkBFnqvCF4jhqPSdE2HBPJiwaf67tgfGR' => 'ACF Hot Wallet (old)',
            'ANvR7ny44GrLy4NTfuVqjGYr4EAwK7vnkW' => 'Altilly',
            'AXxNbmaKspf9UqgKhfTRDdn89NidP2gXWh' => 'ARK Bounty',
            'AYCTHSZionfGoQsRnv5gECEuFWcZXS38gs' => 'ARK Bounty Hot Wallet',
            'AZmQJ2P9xg5j6VPZWjcTzWDD4w7Qww2KGX' => 'ARK GitHub Bounty',
            'ANkHGk5uZqNrKFNY5jtd4A88zzFR3LnJbe' => 'ARK Hot Wallet',
            'AHJJ29sCdR5UNZjdz3BYeDpvvkZCGBjde9' => 'ARK Shield',
            'AdTyTzaXPtj1J1DzTgVksa9NYdUuXCRbm1' => 'ARK Shield (old)',
            'AXzxJ8Ts3dQ2bvBR1tPE7GUee9iSEJb8HX' => 'ARK Team',
            'AUDud8tvyVZa67p3QY7XPRUTjRGnWQQ9Xv' => 'ARK Team (old)',
            'AFrPtEmzu6wdVpa2CnRDEKGQQMWgq8nE9V' => 'Binance',
            'AQkyi31gUbLuFp7ArgH9hUCewg22TkxWpk' => 'Binance Cold Wallet',
            'AdS7WvzqusoP759qRo6HDmUz2L34u4fMHz' => 'Binance Cold Wallet II',
            'Aakg29vVhQhJ5nrsAHysTUqkTBVfmgBSXU' => 'Binance Cold Wallet III',
            'AazoqKvZQ7HKZMQ151qaWFk6nDY1E9faYu' => 'Binance Cold Wallet IV',
            'AUexKjGtgsSpVzPLs6jNMM6vJ6znEVTQWK' => 'Bittrex',
            'AdA5THjiVFAWhcMo5QyTKF1Y6d39bnPR2F' => 'Changelly',
            'AcPwcdDbrprJf8FNCE3dKZaTvPJT8y4Cqi' => 'COSS',
            'AJbmGnDAx9y91MQCDApyaqZhn6fBvYX9iJ' => 'Cryptopia',
            'AewxfHQobSc49a4radHp74JZCGP8LRe4xA' => 'Genesis Wallet',
            'AcVHEfEmFJkgoyuNczpgyxEA3MZ747DRAu' => 'Livecoin',
            'AZcK6t1P9Z2ndiYvdVaS7srzYbTn5DHmck' => 'OKEx',
            'ANQftoXeWoa9ud9q9dd2ZrUpuKinpdejAJ' => 'Upbit',
            'AdzbhuDTyhnfAqepZzVcVsgd1Ym6FgETuW' => 'Upbit Cold Wallet',
            'AReY3W6nTv3utiG2em5nefKEsGQeqEVPN4' => 'Upbit Hot Wallet',
        ],
    ]);

    expect($this->subject->knownWallets())->toBeArray();
    expect($this->subject->knownWallets())->toHaveCount(26);
});

it('should determine if the network currency can be exchanged', function () {
    expect($this->subject->canBeExchanged())->toBeTrue();
});

it('should have a host', function () {
    expect($this->subject->host())->toBe('https://wallets.ark.io/api');
});
