<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia\Concerns;

trait WithFilters
{
    protected function hasFilter(string $name, bool $defaultValue): bool
    {
        if (request()->has($name)) {
            return request()->boolean($name);
        }

        return $defaultValue;
    }

    protected function defaultFilters(?string $group = null): array
    {
        if (!defined('self::FILTERS')) {
            return [];
        }

        /** @var array<string, bool> $filters */
        $filters = constant('self::FILTERS');

        if ($group !== null) {
            if (!array_key_exists($group, $filters)) {
                return [];
            }

            $filters = $filters[$group];
        }

        return $filters;
    }

    protected function filters(?string $group = null): array
    {
        $filters = $this->defaultFilters($group);

        return collect($filters)
            ->keys()
            ->mapWithKeys(fn ($filterName) => [$filterName => $this->hasFilter($filterName, $filters[$filterName])])
            ->toArray();
    }

    protected function hasFilters(?string $group = null): bool
    {
        $filters = $this->defaultFilters($group);

        return collect($filters)
            ->keys()
            ->some(fn ($filterName) => $this->hasFilter($filterName, $filters[$filterName]));
    }
}
