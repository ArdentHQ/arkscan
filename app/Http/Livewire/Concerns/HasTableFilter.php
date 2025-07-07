<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use Illuminate\Support\Arr;

trait HasTableFilter
{
    public bool $selectAllFilters = true;

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

    public function updatingFilter($value, $key): void
    {
        if (Arr::get($this->filter, $key) === $value) {
            return;
        }

        $this->setPage(1);
    }

    public function updatedFilter(): void
    {
        $this->selectAllFilters = $this->isAllSelected;
    }
}
