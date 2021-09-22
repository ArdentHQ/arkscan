<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Fees\Historical;

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
            ->cursor()
            ->groupBy(fn ($date) => Timestamp::fromGenesis($date->timestamp)->format($format))
            ->mapWithKeys(fn ($transactions, $day) => [$day => $transactions->sumBigNumber('fee')->toNumber() / 1e8])
            ->collect();
    }
}
