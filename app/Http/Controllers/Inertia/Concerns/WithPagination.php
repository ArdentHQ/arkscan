<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia\Concerns;

use Illuminate\Support\Str;

trait WithPagination
{
    protected function page(): int
    {
        return (int) request()->get('page');
    }

    protected function perPage(string $name = 'default'): int
    {
        $constantName = Str::upper($name).'_PER_PAGE';  // e.g. VALIDATORS_PER_PAGE

        if (defined(static::class.'::'.$constantName)) {
            return constant(static::class.'::'.$constantName);
        }

        return (int) request()->get('per-page', config('arkscan.pagination.per_page'));
    }
}
