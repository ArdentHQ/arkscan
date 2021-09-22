<?php

declare(strict_types=1);

namespace App\Services\Transactions;

use App\Actions\CacheNetworkHeight;
use App\Facades\Network;
use App\Models\Transaction;

final class TransactionState
{
    public function __construct(private Transaction $transaction)
    {
    }

    public function isConfirmed(): bool
    {
        $confirmations = CacheNetworkHeight::execute() - $this->transaction->block_height;

        return abs($confirmations) >= Network::confirmations();
    }
}
