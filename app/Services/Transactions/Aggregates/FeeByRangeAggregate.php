<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates;

use App\Services\NumberFormatter;
use App\Services\Timestamp;
use App\Services\Transactions\Aggregates\Concerns\HasQueries;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class FeeByRangeAggregate
{
    use HasQueries;

    public function aggregate(Carbon $start, Carbon $end, string $format = 'Y-m-d'): Collection
    {
        $cacheKey = sprintf('fees-by-range:%s:%s', $start->unix(), $end->unix());

        return Cache::remember($cacheKey, 3600, function () use ($start, $end, $format) {
            return $this
                ->dateRangeQuery($start, $end)
                ->orderByDesc('timestamp')
                ->get()
                ->groupBy(fn ($date) => Timestamp::fromGenesis($date->timestamp)->format($format))
                ->mapWithKeys(fn ($transactions, $day) => [$day => NumberFormatter::satoshi($transactions->sum('fee'))]);
        });
    }
}
