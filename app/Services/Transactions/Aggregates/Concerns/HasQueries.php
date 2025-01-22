<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Concerns;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait HasQueries
{
    private function dateRangeQuery(Carbon $start, Carbon $end): Builder
    {
        return Transaction::query()->whereBetween('timestamp', [
            $start->getTimestampMs(),
            $end->getTimestampMs(),
        ]);
    }
}
