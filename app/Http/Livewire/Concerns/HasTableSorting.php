<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Enums\SortDirection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HasTableSorting
{
    use NormalizesConstantPrefixes;
    use WithHooks;

    public array $sortKeys = [];

    /** @var SortDirection[] $sortDirections */
    public array $sortDirections = [];

    public function mountHasTableSorting(): void
    {
        foreach ($this->sortDirections as $name => $direction) {
            if (! is_string($direction)) {
                continue;
            }

            $this->sortDirections[$name] = SortDirection::from(Str::lower($direction));
        }
    }

    public function queryStringHasTableSorting(): array
    {
        $queryString = [
            'sortKeys.default' => ['as' => 'sort', 'except' => static::defaultSortKey()],
            'sortDirections.default' => ['as' => 'sort-direction', 'except' => static::defaultSortDirection()->value],
        ];

        return $queryString;
    }

    public function sortBy(string $sortKey, string $name = 'default'): void
    {
        $currentSortKey = Arr::get($this->sortKeys, $name);
        if ($currentSortKey === $sortKey) {
            if (Arr::get($this->sortDirections, $name) === SortDirection::ASC) {
                $this->setSortDirection(SortDirection::DESC, $name);
                $this->setWithHooks('sortDirections', SortDirection::DESC, $name);
            } else {
                $this->setSortDirection(SortDirection::ASC, $name);
                $this->setWithHooks('sortDirections', SortDirection::ASC, $name);
            }
        } else {
            $this->setSortKey($sortKey, $name);
            $this->setWithHooks('sortKeys', $sortKey, $name);

            $this->setSortDirection(SortDirection::ASC, $name);
            $this->setWithHooks('sortDirections', SortDirection::ASC, $name);
        }

        $this->gotoPage(1, name: $name);
    }

    public function getSortKey(string $name = 'default'): string
    {
        return Arr::get($this->sortKeys, $name, static::defaultSortKey($name));
    }

    public function setSortKey(string $key, string $name = 'default'): void
    {
        data_set($this->sortKeys, $name, $key);
    }

    public function getSortDirection(string $name = 'default'): SortDirection
    {
        return Arr::get($this->sortDirections, $name, static::defaultSortDirection($name));
    }

    public function setSortDirection(SortDirection $direction, string $name = 'default'): void
    {
        data_set($this->sortDirections, $name,  $direction);
    }

    public static function defaultSortKey(string $prefix = ''): string
    {
        $prefix = self::normalizePrefix($prefix, 'default');

        return constant(static::class.'::'.$prefix.'INITIAL_SORT_KEY');
    }

    public static function defaultSortDirection(string $prefix = ''): SortDirection
    {
        $prefix = self::normalizePrefix($prefix, 'default');

        return constant(static::class.'::'.$prefix.'INITIAL_SORT_DIRECTION');
    }

    protected function resolveSortKey(?string $default = null): string
    {
        if (request()->has('sort')) {
            return request()->get('sort');
        }

        return $default ?? static::defaultSortKey();
    }

    protected function resolveSortDirection(?SortDirection $default = null): SortDirection
    {
        if (request()->has('sort-direction')) {
            $sortDirection = request()->get('sort-direction');
            if ($sortDirection === SortDirection::DESC->value) {
                return SortDirection::DESC;
            }

            return SortDirection::ASC;
        }

        return $default ?? static::defaultSortDirection();
    }
}
