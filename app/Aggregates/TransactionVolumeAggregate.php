<?php

declare(strict_types=1);

namespace App\Aggregates;

use App\Contracts\Aggregate;
use App\Models\Transaction;
use App\Services\BigNumber;

final class TransactionVolumeAggregate implements Aggregate
{
    public function aggregate(): string
    {
        return strval(BigNumber::new(Transaction::sum('amount'))->toInt());
    }
}
