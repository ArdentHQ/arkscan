<?php

declare(strict_types=1);

use App\DTO\MemoryWallet;
use App\Services\Cache\WalletCache;

it('should make an instance from a address', function () {
    $subject =  MemoryWallet::fromAddress('D6Z26L69gdk9qYmTv5uzk3uGepigtHY4ax');

    expect($subject->address())->toBe('D6Z26L69gdk9qYmTv5uzk3uGepigtHY4ax');
    expect($subject->publicKey())->toBeNull();
    expect($subject->username())->toBeNull();
    expect($subject->isDelegate())->toBeFalse();
});

it('should make an instance from a public key', function () {
    $subject =  MemoryWallet::fromPublicKey('03d3fdad9c5b25bf8880e6b519eb3611a5c0b31adebc8455f0e096175b28321aff');

    expect($subject->address())->toBe('D6Z26L69gdk9qYmTv5uzk3uGepigtHY4ax');
    expect($subject->publicKey())->toBe('03d3fdad9c5b25bf8880e6b519eb3611a5c0b31adebc8455f0e096175b28321aff');
    expect($subject->username())->toBeNull();
    expect($subject->isDelegate())->toBeFalse();
});

it('should be a delegate', function () {
    (new WalletCache())->setUsernameByAddress('D6Z26L69gdk9qYmTv5uzk3uGepigtHY4ax', 'username');

    $subject =  MemoryWallet::fromPublicKey('03d3fdad9c5b25bf8880e6b519eb3611a5c0b31adebc8455f0e096175b28321aff');

    expect($subject->address())->toBe('D6Z26L69gdk9qYmTv5uzk3uGepigtHY4ax');
    expect($subject->publicKey())->toBe('03d3fdad9c5b25bf8880e6b519eb3611a5c0b31adebc8455f0e096175b28321aff');
    expect($subject->username())->toBe('username');
    expect($subject->isDelegate())->toBeTrue();
});
