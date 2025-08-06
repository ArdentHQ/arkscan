<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates;

use App\Models\Transaction;

final class LargestTransactionAggregate
{
    public function aggregate(): ?Transaction
    {
        return Transaction::orderBy('value', 'desc')
            ->limit(1)
            ->first();
    }
}
