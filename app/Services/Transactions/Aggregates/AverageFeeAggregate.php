<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates;

use App\Services\Transactions\Aggregates\Concerns\HasQueries;
use Carbon\Carbon;

final class AverageFeeAggregate
{
    use HasQueries;

    public function aggregate(Carbon $start, Carbon $end): float
    {
        return $this->dateRangeQuery($start, $end)->avg('fee') / 1e8;
    }
}
