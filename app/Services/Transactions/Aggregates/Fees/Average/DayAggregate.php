<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Fees\Average;

use Carbon\Carbon;

final class DayAggregate
{
    public function aggregate(): float
    {
        return (new RangeAggregate())->aggregate(Carbon::now()->subDay()->addHour(), Carbon::now());
    }
}
