<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\Support\Facades\Log;

/** @property int $perPage */
trait HasTablePagination
{
    use HasPagination;

    public ?int $perPage = null;

    final public function mountHasTablePagination(): void
    {
        if ($this->perPage === null) {
            $this->perPage = static::defaultPerPage();
        } else {
            $this->perPage = $this->resolvePerPage();
        }
        Log::debug('mountHasTablePagination - '.get_class($this), [
            'perPage' => $this->perPage,
            'paginators' => $this->paginators,
        ]);
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

        Log::debug('setPerPage', [
            'perPage' => $this->perPage,
        ]);
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
                Log::debug('defaultPerPage 1 - '.static::class, [
                    'perPage' => $const,
                ]);

                return $const;
            }
        }

        Log::debug('defaultPerPage 2 - '.static::class, [
            'perPage' => intval(config('arkscan.pagination.per_page')),
        ]);

        return intval(config('arkscan.pagination.per_page'));
    }

    private function resolvePerPage(): int
    {
        Log::debug('resolvePerPage', [
            'perPage' => $this->perPage,
        ]);

        return $this->perPage;
    }
}
