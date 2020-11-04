<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Fees\Historical;

use App\Services\Transactions\Aggregates\Fees\Concerns\HasPlaceholders;
use App\Services\Transactions\Aggregates\Fees\Concerns\HasQueries;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final class MonthAggregate
{
    use HasPlaceholders;
    use HasQueries;

    public function aggregate(): Collection
    {
        return $this->mergeWithPlaceholders(
            (new RangeAggregate())->aggregate(Carbon::now()->subDays(30)->startOfDay(), Carbon::now()->endOfDay(), 'd.m'),
            $this->placeholders(Carbon::now()->startOfYear()->diffInDays() * 86400, 30 * 86400, 86400, 'd.m')->take(30)
        );
    }
}
