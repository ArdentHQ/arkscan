<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

trait HasTableFilter
{
    public bool $selectAllFilters = true;

    private bool $previousSelectAllFilters = true;

    public function __get(mixed $property): mixed
    {
        if (array_key_exists($property, $this->filter)) {
            return $this->filter[$property];
        }

        return parent::__get($property);
    }

    public function __set(string $property, mixed $value): void
    {
        if (array_key_exists($property, $this->filter)) {
            $this->filter[$property] = $value;
        }
    }

    abstract public function getNoResultsMessageProperty(): null|string;

    public function queryStringHasTableFilter(): array
    {
        $queryString = [];
        foreach ($this->filter as $key => $value) {
            $queryString['filter.'.$key] = ['as' => 'filter-'.$key, 'except' => $value, 'history' => true];
        }

        return $queryString;
    }

    public function mountHasTableFilter(): void
    {
        foreach ($this->filter as $key => $filter) {
            if (in_array($filter, ['1', 'true', true], true)) {
                $this->filter[$key] = true;
            } elseif (in_array($filter, ['0', 'false', false], true)) {
                $this->filter[$key] = false;
            }
        }

        $this->selectAllFilters = $this->getIsAllSelectedProperty();
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

    public function updatingFilter(): void
    {
        $this->previousSelectAllFilters = $this->selectAllFilters;
    }

    public function updatedFilter(): void
    {
        $this->selectAllFilters = $this->isAllSelected;

        if ($this->selectAllFilters !== $this->previousSelectAllFilters) {
            $this->setPage(1);
        }
    }
}
