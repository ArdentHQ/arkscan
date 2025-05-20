<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates;

use App\Models\Transaction;

final class LargestTransactionAggregate
{
    public function aggregate(): ?Transaction
    {
        // TODO: add transaction type for transfer - https://app.clickup.com/t/86dvxzh7f
        return Transaction::orderBy('value', 'desc')->limit(1)->first();
    }
}
