<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Type;

use App\Models\Scopes\ValidatorResignationScope;
use App\Models\Transaction;

final class ValidatorResignationAggregate
{
    public function aggregate(): int
    {
        return Transaction::withScope(ValidatorResignationScope::class)->count();
    }
}
