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
        $this->perPage = static::defaultPerPage();
    }

    public function queryStringHasTablePagination(): array
    {
        return [
            'perPage' => ['except' => static::defaultPerPage()],
        ];
    }

    public function setPerPage(int $perPage): void
    {
        if (! in_array($perPage, $this->perPageOptions(), true)) {
            return;
        }

        $this->perPage = $perPage;

        $this->gotoPage(1);
    }

    public function perPageOptions(): array
    {
        return trans('pagination.per_page_options');
    }

    public static function defaultPerPage(): int
    {
        if (defined(static::class.'::PER_PAGE')) {
            $const = constant(static::class.'::PER_PAGE');
            // @phpstan-ignore-next-line
            if (is_int($const)) {
                return $const;
            }
        }

        return intval(config('arkscan.pagination.per_page'));
    }
}
