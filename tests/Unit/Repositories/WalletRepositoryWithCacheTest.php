<?php

declare(strict_types=1);

// @TODO: assert that cache has been called

use App\Models\Wallet;
use App\Repositories\WalletRepository;
use App\Repositories\WalletRepositoryWithCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

beforeEach(fn () => $this->subject = new WalletRepositoryWithCache(new WalletRepository()));

it('should create a query for all wallets with a username', function () {
    expect($this->subject->allWithUsername())->toBeInstanceOf(Builder::class);
});

it('should create a query for all wallets with a vote', function () {
    expect($this->subject->allWithVote())->toBeInstanceOf(Builder::class);
});

it('should create a query for all wallets with a public key', function () {
    expect($this->subject->allWithPublicKey())->toBeInstanceOf(Builder::class);
});

it('should create a query for all wallets with a multi signature', function () {
    expect($this->subject->allWithMultiSignature())->toBeInstanceOf(Builder::class);
});

it('should find a wallet by address', function () {
    $wallet = Wallet::factory()->create();

    expect($this->subject->findByAddress($wallet->address))->toBeInstanceOf(Wallet::class);
});

it('should find a wallet by public key', function () {
    $wallet = Wallet::factory()->create();

    expect($this->subject->findByPublicKey($wallet->public_key))->toBeInstanceOf(Wallet::class);
});

it('should find wallets by public keys', function () {
    $wallet = Wallet::factory()->create();

    expect($this->subject->findByPublicKeys([$wallet->public_key]))->toBeInstanceOf(Collection::class);
});

it('should find a wallet by username', function () {
    $wallet = Wallet::factory()->create();

    expect($this->subject->findByUsername($wallet->attributes['delegate']['username']))->toBeInstanceOf(Wallet::class);
});
