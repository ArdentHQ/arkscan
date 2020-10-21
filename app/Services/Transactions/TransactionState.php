<?php

declare(strict_types=1);

namespace App\Services\Transactions;

use App\Facades\Network;
use App\Models\Transaction;
use App\Services\Blockchain\NetworkStatus;

final class TransactionState
{
    private Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function isConfirmed(): bool
    {
        $block = $this->transaction->block;

        if (is_null($block)) {
            return false;
        }

        $confirmations = NetworkStatus::height() - $block->height;

        return $confirmations >= Network::confirmations();
    }
}
