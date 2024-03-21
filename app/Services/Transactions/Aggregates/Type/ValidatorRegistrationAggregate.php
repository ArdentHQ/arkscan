<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Type;

use App\Models\Scopes\ValidatorRegistrationScope;
use App\Models\Transaction;

final class ValidatorRegistrationAggregate
{
    public function aggregate(): int
    {
        return Transaction::withScope(ValidatorRegistrationScope::class)->count();
    }
}
