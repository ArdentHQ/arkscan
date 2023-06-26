<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasPagination;

trait HasTablePagination
{
    use HasPagination;

    public int $perPage;

    public function bootHasTablePagination(): void
    {
        $this->perPage = $this->defaultPerPage();
    }

    public function queryStringHasTablePagination(): array
    {
        return [
            'perPage' => ['except' => $this->defaultPerPage()],
        ];
    }

    public function setPerPage(int $perPage): void
    {
        if (! in_array($perPage, trans('pagination.per_page_options'), true)) {
            return;
        }

        $this->perPage = $perPage;

        $this->gotoPage(1);
    }

    public static function defaultPerPage(): int
    {
        if (defined(static::class.'::PER_PAGE')) {
            $const = constant(static::class.'::PER_PAGE');
            if (is_int($const)) {
                return $const;
            }
        }

        return intval(config('arkscan.pagination.per_page'));
    }
}
