<?php

declare(strict_types=1);

namespace App\Models\Composers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class ValueRangeComposer
{
    public static function compose(Builder $query, array $parameters, string $column, bool $useSatoshi = true): Builder
    {
        $from = Arr::get($parameters, Str::camel($column).'From');
        $to   = Arr::get($parameters, Str::camel($column).'To');

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
