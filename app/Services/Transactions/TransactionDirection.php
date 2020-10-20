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
        return $this->transaction->sender->address === $address;
    }

    public function isReceived(string $address): bool
    {
        return $this->transaction->recipient->address === $address;
    }
}
