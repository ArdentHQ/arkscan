<?php

declare(strict_types=1);

namespace App\Services\Transactions;

use App\Models\Transaction;
use App\Services\Identity;

final class TransactionDirection
{
    public function __construct(private Transaction $transaction)
    {
    }

    public function isSent(string $address): bool
    {
        return Identity::address($this->transaction->sender_public_key) === $address;
    }

    public function isReceived(string $address): bool
    {
        return $this->transaction->recipient_id === $address;
    }
}
