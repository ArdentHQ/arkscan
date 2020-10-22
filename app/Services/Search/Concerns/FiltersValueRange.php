<?php

declare(strict_types=1);

namespace App\Services\Search\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait FiltersValueRange
{
    private function queryValueRange(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from && $to) {
            $query->whereBetween('total_amount', [$from, $to]);
        } elseif ($from) {
            $query->where('total_amount', '>=', $from);
        } elseif ($to) {
            $query->where('total_amount', '<=', $to);
        }

        return $query;
    }
}
