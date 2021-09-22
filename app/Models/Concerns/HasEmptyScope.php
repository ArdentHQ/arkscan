<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasEmptyScope
{
    /**
     * Used to force a query with no results.
     */
    public function scopeEmpty(Builder $query): Builder
    {
        return $query->whereRaw('false');
    }
}
