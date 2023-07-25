<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

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
