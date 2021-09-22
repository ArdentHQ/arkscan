<?php

declare(strict_types=1);

namespace App\Services\Transactions;

use App\Models\Transaction;

final class TransactionStateIcon
{
    private TransactionState $state;

    public function __construct(Transaction $transaction)
    {
        $this->state = new TransactionState($transaction);
    }

    public function name(): string
    {
        if ($this->state->isConfirmed()) {
            return 'confirmed';
        }

        return 'unknown';
    }
}
