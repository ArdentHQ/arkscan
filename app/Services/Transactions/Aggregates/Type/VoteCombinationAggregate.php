<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Type;

use App\Models\Transaction;

final class VoteCombinationAggregate
{
    public function aggregate(): int
    {
        return Transaction::count(); // TODO: add transaction type scope - https://app.clickup.com/t/86dur8fj6
    }
}
