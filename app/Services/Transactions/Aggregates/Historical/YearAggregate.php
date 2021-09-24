<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Historical;

use App\Services\Transactions\Aggregates\Concerns\HasPlaceholders;
use App\Services\Transactions\Aggregates\Concerns\HasQueries;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final class YearAggregate
{
    use HasPlaceholders;
    use HasQueries;

    public function aggregate(): Collection
    {
        return $this->mergeWithPlaceholders(
            (new RangeAggregate())->aggregate(Carbon::now()->subDays(365)->addMonth(), Carbon::now()->addMonth(), 'Mon'),
            $this->placeholders((int) Carbon::now()->subDays(365)->addMonth()->timestamp, (int) Carbon::now()->addMonth()->timestamp, 86400, 'M')->take(365)
        );
    }
}
