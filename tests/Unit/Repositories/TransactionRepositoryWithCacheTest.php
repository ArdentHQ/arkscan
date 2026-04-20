<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use App\Repositories\TransactionRepositoryWithCache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Tests\Stubs\TransactionRepositoryStub;

beforeEach(function () {
    Cache::tags('transactions')->flush();

    $this->subject = new TransactionRepositoryWithCache(new TransactionRepository());
});

it('should find all transactions by wallet', function () {
    $wallet = Transaction::factory(10)->create()[0]->sender;

    expect($this->subject->allByWallet($wallet->address, $wallet->public_key))->toBeInstanceOf(Collection::class);
});

it('should find all transactions by sender', function () {
    $wallet = Transaction::factory(10)->create()[0]->sender;

    expect($this->subject->allBySender($wallet->public_key))->toBeInstanceOf(Collection::class);
});

it('should find all transactions by recipient', function () {
    $wallet = Transaction::factory(10)->create()[0]->to;

    expect($this->subject->allByRecipient($wallet))->toBeInstanceOf(Collection::class);
});

it('should find a transaction by hash', function () {
    $transactionHash = Transaction::factory()->create()->hash;

    expect($this->subject->findByHash($transactionHash))->toBeInstanceOf(Transaction::class);
});

it('should cache the transaction lookups', function () {
    $repository = new TransactionRepositoryStub(Transaction::factory()->make());
    $subject    = new TransactionRepositoryWithCache($repository);

    $subject->allByWallet('wallet-address', 'wallet-public-key');
    $subject->allByWallet('wallet-address', 'wallet-public-key');
    $subject->allBySender('sender-public-key');
    $subject->allBySender('sender-public-key');
    $subject->allByRecipient('recipient-address');
    $subject->allByRecipient('recipient-address');
    $subject->findByHash('transaction-hash');
    $subject->findByHash('transaction-hash');

    expect($repository->allByWalletCalls)->toBe(1)
        ->and($repository->allBySenderCalls)->toBe(1)
        ->and($repository->allByRecipientCalls)->toBe(1)
        ->and($repository->findByHashCalls)->toBe(1);
});
