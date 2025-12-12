<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia\Concerns;

use App\Enums\SortDirection;
use Illuminate\Support\Str;

trait WithFilters
{
    private function hasFilter(string $name, bool $defaultValue ): bool
    {
        if (request()->has($name)) {
            return request()->boolean($name);
        }

        return $defaultValue;
    }

    
}
