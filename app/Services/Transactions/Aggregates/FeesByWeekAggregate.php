<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates;

use App\Services\Transactions\Aggregates\Concerns\HasPlaceholders;
use App\Services\Transactions\Aggregates\Concerns\HasQueries;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final class FeesByWeekAggregate
{
    use HasPlaceholders;
    use HasQueries;

    public function aggregate(): Collection
    {
        return $this->mergeWithPlaceholders(
            (new FeesByRangeAggregate())->aggregate(Carbon::now()->subDays(7)->startOfDay(), Carbon::now()->endOfDay(), 'd.m'),
            $this->placeholders(Carbon::now()->startOfYear()->diffInDays() * 86400, 7 * 86400, 86400, 'd.m')->take(7)
        );
    }
}
