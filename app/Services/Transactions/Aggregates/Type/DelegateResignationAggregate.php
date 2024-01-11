<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Type;

use App\Models\Scopes\DelegateResignationScope;
use App\Models\Transaction;

final class DelegateResignationAggregate
{
    public function aggregate(): int
    {
        return Transaction::withScope(DelegateResignationScope::class)->count();
    }
}
