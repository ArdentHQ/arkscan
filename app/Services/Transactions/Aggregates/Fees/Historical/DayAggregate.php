<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Fees\Historical;

use App\Services\Transactions\Aggregates\Concerns\HasPlaceholders;
use App\Services\Transactions\Aggregates\Concerns\HasQueries;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final class DayAggregate
{
    use HasPlaceholders;
    use HasQueries;

    public function aggregate(): Collection
    {
        return $this->mergeWithPlaceholders(
            (new RangeAggregate())->aggregate(Carbon::now()->subDay()->addHour(), Carbon::now(), 'HH24'),
            $this->placeholders((int) Carbon::now()->subDay()->addHour()->timestamp, (int) Carbon::now()->timestamp, 3600, 'H')->take(24)
        );
    }
}
