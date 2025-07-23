<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use Illuminate\Support\Arr;

trait HasTableFilter
{
    public bool $selectAllFilters = true;

    public array $filters = [];

    abstract public function getNoResultsMessageProperty(): null|string;

    public function mountHasTableFilter(): void
    {
        foreach ($this->defaultFilters() as $name => $filters) {
            if (! array_key_exists($name, $this->filters)) {
                $this->filters[$name] = [];
            }

            foreach ($filters as $key => $filter) {
                if (array_key_exists($key, $this->filters[$name])) {
                    continue;
                }

                $this->filters[$name][$key] = $filter;
            }
        }

        foreach ($this->filters as $name => $filters) {
            foreach ($filters as $key => $filter) {
                if (in_array($filter, ['1', 'true', true], true)) {
                    $this->filters[$name][$key] = true;
                } elseif (in_array($filter, ['0', 'false', false], true)) {
                    $this->filters[$name][$key] = false;
                }
            }
        }
    }

    public function getFilter(string $filter, string $name = 'default'): ?bool
    {
        return Arr::get($this->filters, $name.'.'.$filter);
    }

    public function getFilters(string $name = 'default'): array
    {
        return Arr::get($this->filters, $name, []);
    }

    public function setFilter(string $filter, bool $value, string $name = 'default'): void
    {
        if (! isset($this->filters[$name])) {
            $this->filters[$name] = [];
        }

        data_set($this->sortDirections[$name], $filter, $value);

        $this->gotoPage(1, name: $name);
    }

    // Must be overridden in the component if a different default is required.
    public function getIsAllSelectedProperty(): bool
    {
        return ! collect($this->filters['default'])->contains(false);
    }

    public function updatedSelectAllFilters(bool $value): void
    {
        foreach (array_keys($this->filters['default']) as $key) {
            $this->filters['default'][$key] = $value;
        }
    }

    public static function defaultFilters(string $prefix = ''): array
    {
        $prefix = self::normalizePrefix($prefix, 'default');

        if (! defined(static::class.'::'.$prefix.'INITIAL_FILTERS')) {
            return [];
        }

        return constant(static::class.'::'.$prefix.'INITIAL_FILTERS');
    }

    protected function resolveFilter(string $filter, ?bool $default = null, string $name = 'default'): ?bool
    {
        if ($default === null) {
            $default = $this->getFilter($filter, $name);
        }

        $requestValue = request()->query($filter, $default);
        if ($requestValue === null) {
            return Arr::get(self::defaultFilters($name), $filter);
        }

        if (is_bool($requestValue)) {
            return $requestValue;
        }

        return $requestValue === 'true';
    }

    protected function resolveFilters(array $filters, string $name = 'default'): array
    {
        $parsedFilters = [];
        foreach ($filters as $filter => $isFiltered) {
            $resolvedFilter = $this->resolveFilter($filter, $isFiltered, $name);
            if ($resolvedFilter === null) {
                continue;
            }

            $parsedFilters[$filter] = $resolvedFilter;
        }

        return $parsedFilters;
    }
}
