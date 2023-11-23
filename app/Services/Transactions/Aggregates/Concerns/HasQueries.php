<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Concerns;

use App\Models\Transaction;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait HasQueries
{
    private function dateRangeQuery(Carbon $start, Carbon $end): Builder
    {
        return Transaction::query()->whereBetween('timestamp', [
            Timestamp::fromUnix($start->unix())->unix() * 1000,
            Timestamp::fromUnix($end->unix())->unix() * 1000,
        ]);
    }

    private function getScopeByType(string $type): ?string
    {
        return data_get(Transaction::TYPE_SCOPES, $type);
    }
}
