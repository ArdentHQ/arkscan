<?php

declare(strict_types=1);

use App\Models\Wallet;
use App\Repositories\WalletRepository;
use App\Repositories\WalletRepositoryWithCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Tests\Stubs\WalletRepositoryStub;

beforeEach(function () {
    Cache::tags('wallets')->flush();

    $this->subject = new WalletRepositoryWithCache(new WalletRepository());
});

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

it('should find a wallet by identifier', function () {
    $wallet = Wallet::factory()->create();

    expect($this->subject->findByIdentifier($wallet->address))->toBeInstanceOf(Wallet::class);
});

it('should find a wallet by case sensitive username', function () {
    Wallet::factory()->create([
        'attributes' => [
            'username' => 'JohnDoe',
        ],
    ]);

    try {
        $this->subject->findByUsername('johndoe');

        $this->fail();
    } catch (Throwable) {
        //
    }

    expect($this->subject->findByUsername('JohnDoe'))->toBeInstanceOf(Wallet::class);
});

it('should find a wallet by case insensitive username', function () {
    Wallet::factory()->create([
        'attributes' => [
            'username' => 'JohnDoe',
        ],
    ]);

    expect($this->subject->findByUsername('johndoe', false))->toBeInstanceOf(Wallet::class);
    expect($this->subject->findByUsername('JohnDoe', false))->toBeInstanceOf(Wallet::class);
});

it('should cache the wallet lookups', function () {
    $repository = new WalletRepositoryStub(Wallet::factory()->make());
    $subject    = new WalletRepositoryWithCache($repository);

    $subject->findByAddress('wallet-address');
    $subject->findByAddress('wallet-address');
    $subject->findByPublicKey('wallet-public-key');
    $subject->findByPublicKey('wallet-public-key');
    $subject->findByPublicKeys(['wallet-public-key']);
    $subject->findByPublicKeys(['wallet-public-key']);
    $subject->findByUsername('JohnDoe');
    $subject->findByUsername('JohnDoe');
    $subject->findByIdentifier('wallet-address');
    $subject->findByIdentifier('wallet-address');

    expect($repository->findByAddressCalls)->toBe(1)
        ->and($repository->findByPublicKeyCalls)->toBe(1)
        ->and($repository->findByPublicKeysCalls)->toBe(1)
        ->and($repository->findByUsernameCalls)->toBe(1)
        ->and($repository->findByIdentifierCalls)->toBe(1);
});
