<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates;

use App\Services\Transactions\Aggregates\Concerns\HasPlaceholders;
use App\Services\Transactions\Aggregates\Concerns\HasQueries;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final class FeesByQuarterAggregate
{
    use HasPlaceholders;
    use HasQueries;

    public function aggregate(): Collection
    {
        return $this->mergeWithPlaceholders(
            (new FeesByRangeAggregate())->aggregate(Carbon::now()->subDays(90)->startOfDay(), Carbon::now()->endOfDay(), 'M'),
            $this->placeholders(Carbon::now()->startOfYear()->diffInDays() * 86400, 90 * 86400, 86400, 'M')->take(3)->reverse()
        );
    }
}
