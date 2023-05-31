<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Models\Transaction;
use App\Services\Search\Traits\ValidatesTerm;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Laravel\Scout\Builder;

final class TransactionSearch implements Search
{
    use ValidatesTerm;

    public function search(string $query, int $limit): Builder | EloquentBuilder
    {
        if ($this->couldBeTransactionID($query)) {
            // We can use a regular query since is a exact match
            return Transaction::where('id', strtolower($query))->limit(1);
        }

        return Transaction::search($query)->take(10);
    }
}
