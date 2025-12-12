<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia\Concerns;

use Illuminate\Support\Str;

trait WithPagination
{
    protected function page(): int
    {
        return (int) request()->get('page') ?? 1;
    }

    protected function perPage(string $name = 'default'): int
    {
        $constantName = Str::of($name)->replace('-', '_')->upper().'_PER_PAGE';  // e.g. VALIDATORS_PER_PAGE

        $perPage = config('arkscan.pagination.per_page');

        if (defined(static::class.'::'.$constantName)) {
            $perPage = constant(static::class.'::'.$constantName);
        }

        return (int) request()->get('per-page', $perPage);
    }
}
