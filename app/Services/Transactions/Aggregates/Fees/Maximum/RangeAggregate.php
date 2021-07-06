<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Fees\Maximum;

use App\Services\BigNumber;
use App\Services\Transactions\Aggregates\Concerns\HasQueries;
use Carbon\Carbon;

final class RangeAggregate
{
    use HasQueries;

    public function aggregate(Carbon $start, Carbon $end): float
    {
        return BigNumber::new($this->dateRangeQuery($start, $end)->max('fee') ?? 0)->toFloat();
    }
}
