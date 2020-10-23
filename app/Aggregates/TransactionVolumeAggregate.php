<?php

declare(strict_types=1);

namespace App\Aggregates;

use App\Contracts\Aggregate;
use App\Models\Transaction;

final class TransactionVolumeAggregate implements Aggregate
{
    public function aggregate(): string
    {
        return strval(intval(Transaction::sum('amount') / 1e8));
    }
}
