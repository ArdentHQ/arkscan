<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia\Concerns;

trait WithPagination
{
    protected function page(): int
    {
        return (int) request()->get('page', 1);
    }

    protected function perPage(): int
    {
        return (int) request()->get('per-page', 25);
    }
}
