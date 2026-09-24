<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Historical;

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
            ->selectRaw('MAX(timestamp) as timestamp')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("to_char(to_timestamp(? + timestamp) AT TIME ZONE 'UTC', ?) as formatted_date", [Network::epoch()->timestamp, $format])
            ->orderBy('formatted_date')
            ->groupBy('formatted_date')
            ->pluck('total', 'formatted_date');
    }
}
