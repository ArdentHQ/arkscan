<?php

declare(strict_types=1);

namespace App\Services\Search\Concerns;

use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait FiltersDateRange
{
    /** @phpstan-ignore-next-line */
    private function queryDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        if (is_null($from) && is_null($to)) {
            return $query;
        }

        if (! is_null($from)) {
            $from = Timestamp::fromUnix(Carbon::parse($from)->unix())->unix();

            $query->where('timestamp', '>=', $from);
        }

        if (! is_null($to)) {
            $to = Timestamp::fromUnix(Carbon::parse($to)->unix())->unix();

            $query->where('timestamp', '<=', $to);
        }

        return $query;
    }
}
