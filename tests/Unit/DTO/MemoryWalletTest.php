<?php

declare(strict_types=1);

use App\DTO\MemoryWallet;
use App\Services\Cache\WalletCache;

it('should make an instance from a address', function () {
    $subject =  MemoryWallet::fromAddress('0x6E4C6817a95263B758bbC52e87Ce8e759eD0B084');

    expect($subject->address())->toBe('0x6E4C6817a95263B758bbC52e87Ce8e759eD0B084');
    expect($subject->publicKey())->toBeNull();
    expect($subject->username())->toBeNull();
    expect($subject->isValidator())->toBeFalse();
});

it('should make an instance from a public key', function () {
    $subject =  MemoryWallet::fromPublicKey('03d3fdad9c5b25bf8880e6b519eb3611a5c0b31adebc8455f0e096175b28321aff');

    expect($subject->address())->toBe('0x6E4C6817a95263B758bbC52e87Ce8e759eD0B084');
    expect($subject->publicKey())->toBe('03d3fdad9c5b25bf8880e6b519eb3611a5c0b31adebc8455f0e096175b28321aff');
    expect($subject->username())->toBeNull();
    expect($subject->isValidator())->toBeFalse();
});

it('should be a validator', function () {
    (new WalletCache())->setValidatorPublicKeyByAddress('0x6E4C6817a95263B758bbC52e87Ce8e759eD0B084', '03d3fdad9c5b25bf8880e6b519eb3611a5c0b31adebc8455f0e096175b28321aff');
    (new WalletCache())->setUsernameByAddress('0x6E4C6817a95263B758bbC52e87Ce8e759eD0B084', 'username');

    $subject =  MemoryWallet::fromPublicKey('03d3fdad9c5b25bf8880e6b519eb3611a5c0b31adebc8455f0e096175b28321aff');

    expect($subject->address())->toBe('0x6E4C6817a95263B758bbC52e87Ce8e759eD0B084');
    expect($subject->publicKey())->toBe('03d3fdad9c5b25bf8880e6b519eb3611a5c0b31adebc8455f0e096175b28321aff');
    expect($subject->username())->toBe('username');
    expect($subject->isValidator())->toBeTrue();
});
