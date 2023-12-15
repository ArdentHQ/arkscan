<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Type;

use App\Models\Scopes\VoteSingleScope;
use App\Models\Transaction;

final class VoteAggregate
{
    public function aggregate(): int
    {
        return Transaction::withScope(VoteSingleScope::class)->count();
    }
}
