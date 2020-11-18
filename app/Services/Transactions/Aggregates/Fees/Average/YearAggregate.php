<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Fees\Average;

use Carbon\Carbon;

final class YearAggregate
{
    public function aggregate(): float
    {
        return (new RangeAggregate())->aggregate(Carbon::now()->subDays(365)->addMonth(), Carbon::now()->addMonth());
    }
}
