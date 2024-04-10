<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Historical;

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
            'MAX(timestamp) as timestamp',
            'COUNT(*) as total',
            sprintf("to_char(to_timestamp(timestamp / 1000) AT TIME ZONE 'UTC', '%s') as formatted_date", $format),
        ];

        return $this
            ->dateRangeQuery($start, $end)
            ->select(DB::raw(implode(', ', $select)))
            ->orderBy('formatted_date')
            ->groupBy('formatted_date')
            ->pluck('total', 'formatted_date');
    }
}
