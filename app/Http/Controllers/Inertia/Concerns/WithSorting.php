<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia\Concerns;

use App\Enums\SortDirection;
use Illuminate\Support\Str;

trait WithSorting
{
    private function sortDirection(string $name = 'default'): SortDirection
    {
        $sortDirection = request()->get('sort-direction');

        if (in_array($sortDirection, [SortDirection::ASC->value, SortDirection::DESC->value], true)) {
            return SortDirection::from($sortDirection);
        }

        $constantName = Str::of($name)->replace('-', '_')->upper().'_INITIAL_SORT_DIRECTION';

        return constant(static::class.'::'.$constantName);
    }

    private function sortKey(string $name = 'default'): string
    {
        $constantName = Str::of($name)->replace('-', '_')->upper().'_INITIAL_SORT_KEY';

        if (defined(static::class.'::'.$constantName)) {
            $defaultSortKey = constant(static::class.'::'.$constantName);
        }

        return request()->get('sort', $defaultSortKey);
    }
}
