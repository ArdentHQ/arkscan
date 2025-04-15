<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Models\Wallet;
use App\Repositories\TransactionRepository;
use App\Services\BigNumber;

beforeEach(fn () => $this->subject = new TransactionRepository());

describe('allByWallet', function () {
    it('should find all transactions', function () {
        $transaction = Transaction::factory()->create();

        $result = $this->subject->allByWallet($transaction->sender->address, $transaction->sender->public_key);

        expect($result->count())->toBe(1);
        expect($result->first()->hash)->toBe($transaction->hash);
    });

    it('should find all vote transactions', function () {
        $wallet = Wallet::factory()->create();

        $transaction = Transaction::factory()
            ->vote($wallet->address)
            ->create();

        $result = $this->subject->allByWallet($wallet->address, $wallet->public_key);

        expect($result->count())->toBe(1);
        expect($result->first()->hash)->toBe($transaction->hash);
    });

    it('should find a multipayment transaction', function () {
        $wallet      = Wallet::factory()->create();
        $otherWallet = Wallet::factory()->create();

        $transaction = Transaction::factory()
            ->multiPayment([$wallet->address], [BigNumber::new(1)])
            ->create();

        Transaction::factory()
            ->transfer()
            ->create();

        $result = $this->subject->allByWallet($wallet->address, $wallet->public_key);

        expect($result->count())->toBe(1);
        expect($result->first()->hash)->toBe($transaction->hash);
    });

    it('should find a multipayment transaction with multiple recipients', function () {
        $wallet      = Wallet::factory()->create();
        $otherWallet = Wallet::factory()->create();

        $transaction = Transaction::factory()
            ->multiPayment([
                $wallet->address,
                $otherWallet->address,
            ], [
                BigNumber::new(1),
                BigNumber::new(1),
            ])
            ->create();

        Transaction::factory()
            ->transfer()
            ->create();

        $result = $this->subject->allByWallet($otherWallet->address, $otherWallet->public_key);

        expect($result->count())->toBe(1);
        expect($result->first()->hash)->toBe($transaction->hash);
    });
});

describe('allBySender', function () {
    it('should find all transactions', function () {
        $transaction = Transaction::factory()->create();

        $result = $this->subject->allBySender($transaction->sender->public_key);

        expect($result->count())->toBe(1);
        expect($result->first()->hash)->toBe($transaction->hash);
    });
});

describe('allByRecipient', function () {
    it('should find all transactions', function () {
        $transaction = Transaction::factory()->create();

        $result = $this->subject->allByRecipient($transaction->recipient_address);

        expect($result->count())->toBe(1);
        expect($result->first()->hash)->toBe($transaction->hash);
    });

    it('should find all vote transactions', function () {
        $wallet = Wallet::factory()->create();

        $transaction = Transaction::factory()
            ->vote($wallet->address)
            ->create();

        $result = $this->subject->allByRecipient($wallet->address);

        expect($result->count())->toBe(1);
        expect($result->first()->hash)->toBe($transaction->hash);
    });
});

describe('findById', function () {
    it('should find a transaction', function () {
        $transactionHash = Transaction::factory()->create()->hash;

        $result = $this->subject->findById($transactionHash);

        expect($result->count())->toBe(1);
        expect($result->first()->hash)->toBe($transactionHash);
    });
});
