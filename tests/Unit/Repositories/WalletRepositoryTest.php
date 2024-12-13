<?php

declare(strict_types=1);

use App\Models\Wallet;
use App\Repositories\WalletRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

beforeEach(fn () => $this->subject = new WalletRepository());

it('should create a query for all wallets with a vote', function () {
    expect($this->subject->allWithVote())->toBeInstanceOf(Builder::class);
});

it('should create a query for all wallets with a public key', function () {
    expect($this->subject->allWithPublicKey())->toBeInstanceOf(Builder::class);
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


it('should find a wallet by identifier if could be public key', function () {
    $wallet = Wallet::factory()->create();

    expect($this->subject->findByIdentifier($wallet->public_key))->toBeInstanceOf(Wallet::class);
});

it('should find a wallet by identifier if could be wallet address', function () {
    $wallet = Wallet::factory()->create();

    expect($this->subject->findByIdentifier($wallet->address))->toBeInstanceOf(Wallet::class);
});

it('should find a wallet by identifier if could be username', function () {
    Wallet::factory()->create([
        'attributes' => [
            'username' => 'johndoe',
        ],
    ]);

    expect($this->subject->findByIdentifier('johndoe'))->toBeInstanceOf(Wallet::class);
});

it('should find nothing when searching for a wallet by identifier that is not anything', function () {
    Wallet::factory()->create();

    $this->subject->findByIdentifier('+');
})->throws(ModelNotFoundException::class);
