<?php

namespace App\Http\Livewire\Concerns;

use Illuminate\Support\Collection;

trait HasLazyLoadingPagination
{
    use HasTablePagination;

    public int $totalCount = 0;

    public function queryStringWithPagination(): array
    {
        return [];
    }

    public function isOnLastPage($pageName = 'page'): bool
    {
        $nextPage = $this->paginators[$pageName] + 1;

        return $nextPage > ceil($this->totalCount / $this->perPage);
    }

    public function nextPage($pageName = 'page'): void
    {
        if ($this->isOnLastPage($pageName)) {
            return;
        }

        $this->setPage($this->paginators[$pageName] + 1, $pageName);
    }
}
