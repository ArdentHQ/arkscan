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
        $this->sortKey = static::defaultSortKey();

        if (request()->has('sort-direction')) {
            $sortDirection = request()->get('sort-direction');
            if ($sortDirection === SortDirection::DESC->value) {
                $this->sortDirection = SortDirection::DESC;
            } else {
                $this->sortDirection = SortDirection::ASC;
            }
        } else {
            $this->sortDirection = static::defaultSortDirection();
        }
    }

    public function queryStringHasTableSorting(): array
    {
        return [
            'sortKey'            => ['as' => 'sort', 'except' => static::defaultSortKey()],
            'sortDirectionQuery' => ['as' => 'sort-direction', 'except' => static::defaultSortDirection()->value],
        ];
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

    public function getSortDirectionQueryProperty(): string
    {
        return $this->sortDirection->value;
    }
}
