<?php

namespace App\Http\Livewire\Concerns;

trait HasTableFilter
{
    public bool $selectAllFilters = true;

    abstract public function getNoResultsMessageProperty(): null|string;

    public function mountHasTableFilter(): void
    {
        foreach ($this->filter as &$filter) {
            if (in_array($filter, ['1', 'true', true], true)) {
                $filter = true;
            } elseif (in_array($filter, ['0', 'false', false], true)) {
                $filter = false;
            }
        }
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
}
