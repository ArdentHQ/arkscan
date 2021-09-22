<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Historical;

use App\Services\Timestamp;
use App\Services\Transactions\Aggregates\Concerns\HasQueries;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final class RangeAggregate
{
    use HasQueries;

    public function aggregate(Carbon $start, Carbon $end, string $format): Collection
    {
        return $this
            ->dateRangeQuery($start, $end)
            ->orderBy('timestamp')
            ->get()
            ->groupBy(fn ($date) => Timestamp::fromGenesis($date->timestamp)->format($format))
            ->mapWithKeys(fn ($transactions, $day) => [$day => $transactions->count()]);
    }
}
