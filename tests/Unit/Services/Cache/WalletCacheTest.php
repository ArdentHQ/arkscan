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
    expect($this->subject->getLastBlock('address'))->toBeArray();

    $this->subject->setLastBlock('address', [1, 2, 3]);

    expect($this->subject->getLastBlock('address'))->toBeArray();
});

it('should get and set the performance', function () {
    expect($this->subject->getPerformance('address'))->toBeArray();

    $this->subject->setPerformance('address', [1, 2, 3]);

    expect($this->subject->getPerformance('address'))->toBeArray();
});

it('should get and set the productivity', function () {
    expect($this->subject->getProductivity('address'))->toBe(-1.0);

    $this->subject->setProductivity('address', 10);

    expect($this->subject->getProductivity('address'))->toBe(10.0);
});

it('should get and set the resignation id', function () {
    expect($this->subject->getResignationId('address'))->toBeNull();

    $this->subject->setResignationId('address', 'id');

    expect($this->subject->getResignationId('address'))->toBeString();
});

it('should get and set the vote', function () {
    expect($this->subject->getVote('address'))->toBeNull();

    $this->subject->setVote('address', Wallet::factory()->create());

    expect($this->subject->getVote('address'))->toBeInstanceOf(Wallet::class);
});

it('should get and set the username by address', function () {
    expect($this->subject->getUsernameByAddress('address'))->toBeNull();

    $this->subject->setUsernameByAddress('address', 'username');

    expect($this->subject->getUsernameByAddress('address'))->toBeString();
});

it('should get and set the missed blocks by address', function () {
    expect($this->subject->getMissedBlocks('address'))->toBe(0);

    $this->subject->setMissedBlocks('address', 1);

    expect($this->subject->getMissedBlocks('address'))->toBe(1);
});
