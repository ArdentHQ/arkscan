<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Type;

use App\Models\Scopes\UsernameResignationScope;
use App\Models\Transaction;

final class UsernameResignationAggregate
{
    public function aggregate(): int
    {
        return Transaction::withScope(UsernameResignationScope::class)
            ->count();
    }
}
