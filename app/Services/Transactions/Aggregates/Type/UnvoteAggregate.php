<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Type;

use App\Models\Scopes\UnvoteSingleScope;
use App\Models\Transaction;

final class UnvoteAggregate
{
    public function aggregate(): int
    {
        return Transaction::withScope(UnvoteSingleScope::class)->count();
    }
}
