<?php

declare(strict_types=1);

namespace App\Services\Transactions;

use App\Facades\Blocks;
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
            $block = Blocks::findById($this->transaction->block_id);

            $confirmations = (new NetworkCache())->getHeight() - $block->height->toNumber();

            return $confirmations >= Network::confirmations();
        } catch (\Throwable $th) {
            return false;
        }
    }
}
