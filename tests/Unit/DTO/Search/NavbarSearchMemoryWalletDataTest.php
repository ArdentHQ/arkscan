<?php

declare(strict_types=1);

use App\DTO\MemoryWallet;
use App\DTO\Search\NavbarSearchMemoryWalletData;
use App\Services\Cache\WalletCache;
use Illuminate\Support\Facades\Cache;

it('returns null when the memory wallet is missing', function () {
    expect(NavbarSearchMemoryWalletData::fromMemoryWallet(null))->toBeNull();
});

it('maps cached metadata from a memory wallet instance', function () {
    Cache::tags('wallet')->flush();

    $address = '0x'.str_repeat('2', 40);

    $walletCache = new WalletCache();
    $walletCache->setWalletNameByAddress($address, 'Cached Username');
    $walletCache->setContractAddresses([$address]);

    $wallet = MemoryWallet::fromAddress($address);

    $data = NavbarSearchMemoryWalletData::fromMemoryWallet($wallet);

    expect($data)->not->toBeNull();
    expect($data->address)->toBe($address);
    expect($data->username)->toBe('Cached Username');
    expect($data->isContract)->toBeTrue();
});
