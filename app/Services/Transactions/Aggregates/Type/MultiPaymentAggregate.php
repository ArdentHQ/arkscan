<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Type;

use App\Models\Scopes\MultiPaymentScope;
use App\Models\Transaction;

final class MultiPaymentAggregate
{
    public function aggregate(): int
    {
        return Transaction::withScope(MultiPaymentScope::class)
            ->count();
    }
}
