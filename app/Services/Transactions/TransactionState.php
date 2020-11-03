<?php

declare(strict_types=1);

namespace App\Services\Transactions;

use App\Facades\Network;
use App\Models\Transaction;
use App\Services\Cache\NetworkCache;

final class TransactionState
{
    private Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function isConfirmed(): bool
    {
        try {
            $confirmations = (new NetworkCache())->getHeight() - $this->transaction->block_height;

            return $confirmations >= Network::confirmations();
        } catch (\Throwable $th) {
            return false;
        }
    }
}
