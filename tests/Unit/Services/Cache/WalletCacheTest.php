<?php

declare(strict_types=1);

use App\Models\Wallet;

use App\Services\Cache\WalletCache;

beforeEach(fn () => $this->subject = new WalletCache());

it('should get and set the known wallets', function () {
    expect($this->subject->getKnown())->toBeArray();
    expect($this->subject->setKnown(fn () => [1, 2, 3]))->toBeArray();
    expect($this->subject->getKnown())->toBeArray();
});

it('should get and set the last block', function () {
    expect($this->subject->getLastBlock('publicKey'))->toBeArray();
    expect($this->subject->setLastBlock('publicKey', fn () => [1, 2, 3]))->toBeArray();
    expect($this->subject->getLastBlock('publicKey'))->toBeArray();
});

it('should get and set the performance', function () {
    expect($this->subject->getPerformance('publicKey'))->toBeArray();
    expect($this->subject->setPerformance('publicKey', fn () => [1, 2, 3]))->toBeArray();
    expect($this->subject->getPerformance('publicKey'))->toBeArray();
});

it('should get and set the productivity', function () {
    expect($this->subject->getProductivity('publicKey'))->toBe(0.0);
    expect($this->subject->setProductivity('publicKey', fn () => 10))->toBe(10.0);
    expect($this->subject->getProductivity('publicKey'))->toBe(10.0);
});

it('should get and set the resignation id', function () {
    expect($this->subject->getResignationId('address'))->toBeNull();
    expect($this->subject->setResignationId('address', fn () => 'id'))->toBeString();
    expect($this->subject->getResignationId('address'))->toBeString();
});

it('should get and set the vote', function () {
    expect($this->subject->getVote('publicKey'))->toBeNull();
    expect($this->subject->setVote('publicKey', fn () => Wallet::factory()->create()))->toBeInstanceOf(Wallet::class);
    expect($this->subject->getVote('publicKey'))->toBeInstanceOf(Wallet::class);
});

it('should get and set the multi signature address', function () {
    expect($this->subject->getMultiSignatureAddress(3, [1, 2, 3]))->toBeNull();
    expect($this->subject->setMultiSignatureAddress(3, [1, 2, 3], fn () => '123'))->toBeString();
    expect($this->subject->getMultiSignatureAddress(3, [1, 2, 3]))->toBeString();
});

it('should get and set the username by address', function () {
    expect($this->subject->getUsernameByAddress('address'))->toBeNull();
    expect($this->subject->setUsernameByAddress('address', 'username'))->toBeString();
    expect($this->subject->getUsernameByAddress('address'))->toBeString();
});

it('should get and set the username by public key', function () {
    expect($this->subject->getUsernameByPublicKey('publicKey'))->toBeNull();
    expect($this->subject->setUsernameByPublicKey('publicKey', 'username'))->toBeString();
    expect($this->subject->getUsernameByPublicKey('publicKey'))->toBeString();
});
