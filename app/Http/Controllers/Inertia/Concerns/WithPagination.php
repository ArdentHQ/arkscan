<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia\Concerns;

use Illuminate\Support\Str;

trait WithPagination
{
    protected function page(string $name = 'default'): int
    {
        $queryKey = $name === 'default' ? 'page' : "{$name}-page";

        if (request()->has($queryKey)) {
            return (int) request()->get($queryKey);
        }

        if ($queryKey !== 'page' && request()->has('page')) {
            return (int) request()->get('page');
        }

        return (int) request()->get($queryKey, 1);
    }

    protected function perPage(string $name = 'default'): int
    {
        if (request()->has('per-page')) {
            return (int) request()->get('per-page');
        }

        $constantName = Str::upper($name).'_PER_PAGE';  // e.g. VALIDATORS_PER_PAGE

        if (defined(static::class.'::'.$constantName)) {
            return constant(static::class.'::'.$constantName);
        }

        return (int) request()->get('per-page', config('arkscan.pagination.per_page'));
    }
}
