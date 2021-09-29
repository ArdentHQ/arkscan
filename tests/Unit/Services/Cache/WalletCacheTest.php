<?php

declare(strict_types=1);

use App\Models\Wallet;
use App\Services\Cache\WalletCache;

beforeEach(fn () => $this->subject = new WalletCache());

it('should get and set the known wallets', function () {
    expect($this->subject->getKnown())->toBeArray();

    $this->subject->setKnown(fn () => [1, 2, 3]);

    expect($this->subject->getKnown())->toBeArray();
});

it('should get and set the last block', function () {
    expect($this->subject->getLastBlock('publicKey'))->toBeArray();

    $this->subject->setLastBlock('publicKey', [1, 2, 3]);

    expect($this->subject->getLastBlock('publicKey'))->toBeArray();
});

it('should get and set the performance', function () {
    expect($this->subject->getPerformance('publicKey'))->toBeArray();

    $this->subject->setPerformance('publicKey', [1, 2, 3]);

    expect($this->subject->getPerformance('publicKey'))->toBeArray();
});

it('should get and set the productivity', function () {
    expect($this->subject->getProductivity('publicKey'))->toBe(-1.0);

    $this->subject->setProductivity('publicKey', 10);

    expect($this->subject->getProductivity('publicKey'))->toBe(10.0);
});

it('should get and set the resignation id', function () {
    expect($this->subject->getResignationId('address'))->toBeNull();

    $this->subject->setResignationId('address', 'id');

    expect($this->subject->getResignationId('address'))->toBeString();
});

it('should get and set the vote', function () {
    expect($this->subject->getVote('publicKey'))->toBeNull();

    $this->subject->setVote('publicKey', Wallet::factory()->create());

    expect($this->subject->getVote('publicKey'))->toBeInstanceOf(Wallet::class);
});

it('should get and set the multi signature address', function () {
    expect($this->subject->getMultiSignatureAddress(3, [1, 2, 3]))->toBeNull();

    $this->subject->setMultiSignatureAddress(3, [1, 2, 3], fn () => '123');

    expect($this->subject->getMultiSignatureAddress(3, [1, 2, 3]))->toBeString();
});

it('should get and set the username by address', function () {
    expect($this->subject->getUsernameByAddress('address'))->toBeNull();

    $this->subject->setUsernameByAddress('address', 'username');

    expect($this->subject->getUsernameByAddress('address'))->toBeString();
});

it('should get and set the username by public key', function () {
    expect($this->subject->getUsernameByPublicKey('publicKey'))->toBeNull();

    $this->subject->setUsernameByPublicKey('publicKey', 'username');

    expect($this->subject->getUsernameByPublicKey('publicKey'))->toBeString();
});

it('should get and set the missed blocks by public key', function () {
    expect($this->subject->getMissedBlocks('publicKey'))->toBe(0);

    $this->subject->setMissedBlocks('publicKey', 1);

    expect($this->subject->getMissedBlocks('publicKey'))->toBe(1);
});
