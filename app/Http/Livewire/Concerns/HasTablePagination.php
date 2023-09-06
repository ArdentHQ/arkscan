<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasPagination;

trait HasTablePagination
{
    use HasPagination;

    public int $perPage = 25;

    final public function initializeHasTablePagination(): void
    {
        $this->perPage = $this->resolvePerPage();
    }

    final public function queryStringHasTablePagination(): array
    {
        return [
            'perPage' => ['except' => static::defaultPerPage()],
        ];
    }

    final public function setPerPage(int $perPage): void
    {
        if (! in_array($perPage, static::perPageOptions(), true)) {
            return;
        }

        $this->perPage = $perPage;

        $this->gotoPage(1);
    }

    // @phpstan-ignore-next-line
    public static function perPageOptions(): array
    {
        return trans('pagination.per_page_options');
    }

    final public static function defaultPerPage(): int
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

    private function resolvePerPage(): int
    {
        return static::defaultPerPage();
    }
}
