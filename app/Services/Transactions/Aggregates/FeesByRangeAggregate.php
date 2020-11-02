<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates;

use App\Services\Cache\NetworkCache;
use App\Services\Timestamp;
use App\Services\Transactions\Aggregates\Concerns\HasQueries;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final class FeesByRangeAggregate
{
    use HasQueries;

    public function aggregate(Carbon $start, Carbon $end, string $format): Collection
    {
        return (new NetworkCache())->setFeesByRange($start, $end, function () use ($start, $end, $format) {
            return $this
                ->dateRangeQuery($start, $end)
                ->orderBy('timestamp')
                ->get()
                ->groupBy(fn ($date) => Timestamp::fromGenesis($date->timestamp)->format($format))
                ->mapWithKeys(fn ($transactions, $day) => [$day => $transactions->sumBigNumber('fee')->toNumber() / 1e8]);
        });
    }
}
