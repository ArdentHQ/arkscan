<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Enums\SortDirection;

trait HasTableSorting
{
    public string $sortKey;

    public SortDirection $sortDirection;

    public function mountHasTableSorting(): void
    {
        $this->sortKey       = static::defaultSortKey();
        $this->sortDirection = $this->resolveSortDirection();
    }

    public function queryStringHasTableSorting(): array
    {
        return $this->getQueryStringValues();
    }

    private function getQueryStringValues(?string $prefix = null): array
    {
        if ($prefix !== null) {
            $prefix = $prefix.'-';
        }

        $queryString = [
            'sortKey' => ['as' => $prefix.'sort', 'except' => static::defaultSortKey(), 'history' => true],
        ];

        if (request()->has('sort-direction')) {
            $sortDirection = request()->get('sort-direction');
            if (in_array($sortDirection, [SortDirection::ASC->value, SortDirection::DESC->value], true)) {
                $queryString['sortDirection'] = ['as' => $prefix.'sort-direction', 'except' => static::defaultSortDirection()->value, 'history' => true];
            }
        }

        return $queryString;
    }

    public function sortBy(string $key): void
    {
        if ($this->sortKey === $key) {
            if ($this->sortDirection === SortDirection::ASC) {
                $this->sortDirection = SortDirection::DESC;
            } else {
                $this->sortDirection = SortDirection::ASC;
            }
        } else {
            $this->sortKey       = $key;
            $this->sortDirection = SortDirection::ASC;
        }

        $this->gotoPage(1);
    }

    public static function defaultSortKey(): string
    {
        return constant(static::class.'::INITIAL_SORT_KEY');
    }

    public static function defaultSortDirection(): SortDirection
    {
        return constant(static::class.'::INITIAL_SORT_DIRECTION');
    }

    private function resolveSortDirection(): SortDirection
    {
        if (request()->has('sort-direction')) {
            $sortDirection = request()->get('sort-direction');
            if ($sortDirection === SortDirection::DESC->value) {
                return SortDirection::DESC;
            }

            return SortDirection::ASC;
        }

        return static::defaultSortDirection();
    }
}
