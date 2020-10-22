<?php

declare(strict_types=1);

namespace App\Services\Search\Concerns;

use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait FiltersDateRange
{
    /** @phpstan-ignore-next-line */
    private function queryDateRange(Builder $query, ?string $dateFrom, ?string $dateTo): Builder
    {
        if (! is_null($dateFrom)) {
            $dateFrom = Timestamp::fromUnix(Carbon::parse($dateFrom)->unix())->unix();
        }

        if (! is_null($dateTo)) {
            $dateTo = Timestamp::fromUnix(Carbon::parse($dateTo)->unix())->unix();
        }

        if (! is_null($dateFrom) && ! is_null($dateTo)) {
            $query->whereBetween('timestamp', [$dateFrom, $dateTo]);
        } elseif (! is_null($dateFrom)) {
            $query->where('timestamp', '>=', $dateFrom);
        } elseif (! is_null($dateTo)) {
            $query->where('timestamp', '<=', $dateTo);
        }

        return $query;
    }
}
