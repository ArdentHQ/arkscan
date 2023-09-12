<?php

namespace App\Http\Livewire\Concerns;

use App\Enums\SortDirection;

trait HasTableSorting
{
    public string $sortKey;

    public SortDirection $sortDirection;

    public function bootHasTableSorting(): void
    {
        $this->sortKey = static::defaultSortKey();
        $this->sortDirection = static::defaultSortDirection();
    }

    public function queryStringHasTableSorting(): array
    {
        return [
            'sortKey' => ['except' => static::defaultSortKey()],
            'sortDirection' => ['except' => static::defaultSortDirection()],
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
            $this->sortKey = $key;
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
}
