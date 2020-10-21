<?php

declare(strict_types=1);

namespace App\Services\Transactions;

use App\Models\Transaction;

final class TransactionDirection
{
    private Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function isSent(string $address): bool
    {
        $wallet = $this->transaction->sender;

        if (is_null($wallet)) {
            return false;
        }

        return $wallet->address === $address;
    }

    public function isReceived(string $address): bool
    {
        $wallet = $this->transaction->recipient;

        if (is_null($wallet)) {
            return false;
        }

        return $wallet->address === $address;
    }
}
