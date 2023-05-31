<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Models\Transaction;
use App\Services\Search\Traits\ValidatesTerm;
use Laravel\Scout\Builder;

final class TransactionSearch implements Search
{
    use ValidatesTerm;

    public function search(string $query): Builder
    {
        if ($this->couldBeTransactionID($query)) {
            // Exact match
            return Transaction::search(sprintf('"%s"', $query));
        }

        return Transaction::search($query);
    }
}
