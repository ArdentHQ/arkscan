<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Type;

use App\Models\Scopes\DelegateRegistrationScope;
use App\Models\Transaction;

final class DelegateRegistrationAggregate
{
    public function aggregate(): int
    {
        return Transaction::withScope(DelegateRegistrationScope::class)->count();
    }
}
