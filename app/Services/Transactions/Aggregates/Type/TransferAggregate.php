<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Type;

use App\Models\Scopes\TransferScope;
use App\Models\Transaction;

final class TransferAggregate
{
    public function aggregate(): int
    {
        return Transaction::withScope(TransferScope::class)->count();
    }
}
