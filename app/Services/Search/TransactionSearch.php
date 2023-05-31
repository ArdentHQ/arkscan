<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Models\Transaction;
use App\Services\Search\Traits\ValidatesTerm;
use Illuminate\Support\Collection;

final class TransactionSearch implements Search
{
    use ValidatesTerm;

    public function search(string $query, int $limit): Collection
    {
        if ($this->couldBeTransactionID($query)) {
            // Quoted so it gets the exact match
            $builder = Transaction::search(sprintf('"%s"', $query))->take(1);
        } else {
            $builder = Transaction::search($query)->take($limit);
        }

        return collect($builder->raw()['hits'])->map(fn ($item) => new Transaction($item));
    }
}
