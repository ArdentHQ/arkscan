<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Fees\Minimum;

use Carbon\Carbon;

final class MonthAggregate
{
    public function aggregate(): float
    {
        return (new RangeAggregate())->aggregate(Carbon::now()->subDays(29), Carbon::now()->addDay());
    }
}
