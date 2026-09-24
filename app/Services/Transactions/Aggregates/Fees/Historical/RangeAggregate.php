<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Fees\Historical;

use App\Facades\Network;
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
            ->selectRaw('SUM(fee) as fee')
            ->selectRaw("to_char(to_timestamp(? + timestamp) AT TIME ZONE 'UTC', ?) as formatted_date", [Network::epoch()->timestamp, $format])
            ->orderBy('formatted_date')
            ->groupBy('formatted_date')
            ->pluck('fee', 'formatted_date')
            ->mapWithKeys(fn ($fee, $date) => [$date => $fee->toFloat()]);
    }
}
