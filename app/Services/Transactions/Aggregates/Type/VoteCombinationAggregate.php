<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Type;

use App\Models\Scopes\VoteCombinationScope;
use App\Models\Transaction;

final class VoteCombinationAggregate
{
    public function aggregate(): int
    {
        return Transaction::withScope(VoteCombinationScope::class)->count();
    }
}
