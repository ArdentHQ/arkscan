<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Type;

use App\Models\Scopes\UsernameRegistrationScope;
use App\Models\Transaction;

final class UsernameRegistrationAggregate
{
    public function aggregate(): int
    {
        return Transaction::withScope(UsernameRegistrationScope::class)
            ->count();
    }
}
