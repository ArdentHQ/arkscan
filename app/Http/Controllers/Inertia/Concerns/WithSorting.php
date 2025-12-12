<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia\Concerns;

use App\Enums\SortDirection;
use Illuminate\Support\Str;

trait WithSorting
{
    private function sortDirection(string $name = 'default'): SortDirection
    {
        $constantName = Str::of($name)->replace('-', '_')->upper().'_INITIAL_SORT_DIRECTION'; 

        if (defined(static::class.'::'.$constantName)) {
            $defaultSortDirection = constant(static::class.'::'.$constantName);
        }

        return request()->get('sort', $defaultSortDirection);
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
