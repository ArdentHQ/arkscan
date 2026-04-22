<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia\Concerns;

trait WithFilters
{
    protected function hasFilter(string $name, bool $defaultValue): bool
    {
        if (request()->has($name)) {
            return request()->query($name) === 'true';
        }

        return $defaultValue;
    }

    protected function defaultFilters(?string $group = null): array
    {
        if (! defined('self::FILTERS')) {
            return [];
        }

        $filters = constant('self::FILTERS');

        if ($group === null) {
            return $filters;
        }

        $groupFilters = data_get($filters, $group, []);

        return is_array($groupFilters) ? $groupFilters : [];
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
