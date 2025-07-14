<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Enums\SortDirection;

trait HasTableSorting
{
    public string $sortKey;

    public SortDirection $sortDirection;

    public string $internalSortKey;

    public SortDirection $internalSortDirection;

    public function mountHasTableSorting(): void
    {
        $this->sortKey       = static::defaultSortKey();
        $this->sortDirection = $this->resolveSortDirection();

        $this->internalSortKey       = $this->sortKey;
        $this->internalSortDirection = $this->sortDirection;
    }

    public function queryStringHasTableSorting(): array
    {
        $queryString = [
            'sortKey' => ['as' => 'sort', 'except' => static::defaultSortKey()],
        ];

        if (request()->has('sort-direction')) {
            $sortDirection = request()->get('sort-direction');
            if (in_array($sortDirection, [SortDirection::ASC->value, SortDirection::DESC->value], true)) {
                $queryString['sortDirection'] = ['as' => 'sort-direction', 'except' => static::defaultSortDirection()->value];
            }
        }

        return $queryString;
    }

    public function sortBy(string $key): void
    {
        if ($this->internalSortKey === $key) {
            if ($this->sortDirection === SortDirection::ASC) {
                $this->sortDirection = SortDirection::DESC;
            } else {
                $this->sortDirection = SortDirection::ASC;
            }
        } else {
            $this->sortKey       = $key;
            $this->sortDirection = SortDirection::ASC;
        }

        $this->internalSortKey       = $this->sortKey;
        $this->internalSortDirection = $this->sortDirection;

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
