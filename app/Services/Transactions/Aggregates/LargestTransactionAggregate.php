<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates;

use App\Models\Transaction;

final class LargestTransactionAggregate
{
    public function aggregate(): ?Transaction
    {
        // TODO: add transaction type for transfer - https://app.clickup.com/t/86dur8fj6
        return Transaction::orderBy('amount', 'desc')->limit(1)->first();
    }
}
