<?php

declare(strict_types=1);

namespace App\Services\Search\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait FiltersValueRange
{
    /**
     * @param string|int|null $from
     * @param string|int|null $to
     */
    private function queryValueRange(Builder $query, string $column, $from, $to, bool $useSatoshi = true): Builder
    {
        if (is_null($from) && is_null($to)) {
            return $query;
        }

        if (! is_null($from) && $from > 0) {
            if ($useSatoshi) {
                $query->where($column, '>=', $from * 1e8);
            } else {
                $query->where($column, '>=', $from);
            }
        }

        if (! is_null($to) && $to > 0) {
            if ($useSatoshi) {
                $query->where($column, '<=', $to * 1e8);
            } else {
                $query->where($column, '<=', $to);
            }
        }

        return $query;
    }
}
