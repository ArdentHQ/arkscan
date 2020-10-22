<?php

declare(strict_types=1);

namespace App\Services\Search\Concerns;

use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait FiltersDateRange
{
    private function queryDateRange(Builder $query, ?string $dateFrom, ?string $dateTo): Builder
    {
        if ($dateFrom) {
            $dateFrom = Timestamp::fromUnix(Carbon::parse($dateFrom)->unix())->unix();
        }

        if ($dateTo) {
            $dateTo = Timestamp::fromUnix(Carbon::parse($dateTo)->unix())->unix();
        }

        if ($dateFrom && $dateTo) {
            $query->whereBetween('timestamp', [$dateFrom, $dateTo]);
        } elseif ($dateFrom) {
            $query->where('timestamp', '>=', $dateFrom);
        } elseif ($dateTo) {
            $query->where('timestamp', '<=', $dateTo);
        }

        return $query;
    }
}
