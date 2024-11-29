<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Type;

use App\Models\Scopes\BatchTransferScope;
use App\Models\Transaction;

final class BatchTransferAggregate
{
    public function aggregate(): int
    {
        return Transaction::withScope(BatchTransferScope::class)
            ->count();
    }
}
