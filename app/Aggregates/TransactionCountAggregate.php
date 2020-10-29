<?php

declare(strict_types=1);

namespace App\Aggregates;

use App\Contracts\Aggregate;
use App\Models\Transaction;

final class TransactionCountAggregate implements Aggregate
{
    public function aggregate(): string
    {
        return (string) Transaction::count();
    }
}
