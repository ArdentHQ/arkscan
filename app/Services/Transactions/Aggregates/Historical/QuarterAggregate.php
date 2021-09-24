<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Historical;

use App\Services\Transactions\Aggregates\Concerns\HasPlaceholders;
use App\Services\Transactions\Aggregates\Concerns\HasQueries;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final class QuarterAggregate
{
    use HasPlaceholders;
    use HasQueries;

    public function aggregate(): Collection
    {
        return $this->mergeWithPlaceholders(
            (new RangeAggregate())->aggregate(Carbon::now()->subDays(89), Carbon::now()->addDay(), 'Mon'),
            $this->placeholders((int) Carbon::now()->subDays(89)->timestamp, (int) Carbon::now()->addDay()->timestamp, 86400, 'M')->reverse()->take(3)->reverse()
        );
    }
}
