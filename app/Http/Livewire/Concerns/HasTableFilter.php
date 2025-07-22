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

    public function getIsAllSelectedProperty(): bool
    {
        return ! collect($this->filter)->contains(false);
    }

    public function updatedSelectAllFilters(bool $value): void
    {
        foreach ($this->filter as &$filter) {
            $filter = $value;
        }
    }

    public function updatedFilter(): void
    {
        $this->selectAllFilters = $this->isAllSelected;

        $this->setPage(1);
    }
}
