<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Fees\Historical;

use App\Facades\Network;
use App\Services\Transactions\Aggregates\Concerns\HasQueries;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class RangeAggregate
{
    use HasQueries;

    public function aggregate(Carbon $start, Carbon $end, string $format): Collection
    {
        $select = [
            'SUM(fee) as fee',
            sprintf("to_char(to_timestamp(%d+timestamp) AT TIME ZONE 'UTC', '%s') as formatted_date", Network::epoch()->timestamp, $format),
        ];

        return $this
            ->dateRangeQuery($start, $end)
            ->select(DB::raw(implode(', ', $select)))
            ->orderBy('formatted_date')
            ->groupBy('formatted_date')
            ->pluck('fee', 'formatted_date')
            ->mapWithKeys(fn ($fee, $date) => [$date => $fee->toFloat()]);
    }
}
