<?php

declare(strict_types=1);

use App\DTO\MemoryWallet;
use App\Facades\Network;
use App\Models\Wallet;
use App\Services\Cache\ContractCache;
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
    $wallet = Wallet::factory()->create();
    (new WalletCache())->setValidator('0x6E4C6817a95263B758bbC52e87Ce8e759eD0B084', $wallet);
    (new WalletCache())->setWalletNameByAddress('0x6E4C6817a95263B758bbC52e87Ce8e759eD0B084', 'username');

    $subject =  MemoryWallet::fromPublicKey('03d3fdad9c5b25bf8880e6b519eb3611a5c0b31adebc8455f0e096175b28321aff');

    expect($subject->address())->toBe('0x6E4C6817a95263B758bbC52e87Ce8e759eD0B084');
    expect($subject->publicKey())->toBe('03d3fdad9c5b25bf8880e6b519eb3611a5c0b31adebc8455f0e096175b28321aff');
    expect($subject->username())->toBe('username');
    expect($subject->isValidator())->toBeTrue();
});

it('should check if address is a known contract', function () {
    $wallet = MemoryWallet::fromAddress(Network::knownContract('consensus'));

    expect($wallet->isContract())->toBeTrue();
});

it('should check if address is not a known contract', function () {
    $wallet = MemoryWallet::fromAddress('0x6E4C6817a95263B758bbC52e87Ce8e759eD0B084');

    expect($wallet->isContract())->toBeFalse();
});

it('should check if address is a deployed contract', function () {
    (new ContractCache())->setContractAddresses(['0x6E4C6817a95263B758bbC52e87Ce8e759eD0B084']);

    $wallet = MemoryWallet::fromAddress('0x6E4C6817a95263B758bbC52e87Ce8e759eD0B084');

    expect($wallet->isContract())->toBeTrue();
});
