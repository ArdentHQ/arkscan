<?php

declare(strict_types=1);

namespace App\Services\Search\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait FiltersValueRange
{
    /** @phpstan-ignore-next-line */
    private function queryValueRange(Builder $query, ?string $from, ?string $to): Builder
    {
        if (! is_null($from) && ! is_null($to)) {
            $query->whereBetween('total_amount', [$from, $to]);
        } elseif (! is_null($from)) {
            $query->where('total_amount', '>=', $from);
        } elseif (! is_null($to)) {
            $query->where('total_amount', '<=', $to);
        }

        return $query;
    }
}
