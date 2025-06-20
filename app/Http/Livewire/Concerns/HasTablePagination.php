<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasPagination;

/** @property int $perPage */
trait HasTablePagination
{
    use HasPagination;

    public ?int $perPage = null;

    final public function mountHasTablePagination(): void
    {
        if ($this->perPage === null) {
            $this->perPage = static::defaultPerPage();
        }
    }

    final public function queryStringHasTablePagination(): array
    {
        return [
            'perPage' => ['except' => static::defaultPerPage(), 'history' => true],
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
}
