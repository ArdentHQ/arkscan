<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\Support\Facades\Log;

/** @property int $perPage */
trait HasTablePagination
{
    use SyncsInput;
    use HasPagination;

    public ?int $perPage = null;

    public ?int $internalPerPage = null;

    final public function mountHasTablePagination(): void
    {
        Log::debug('Mounting HasTablePagination', [
            'class'           => static::class,
            'perPage'         => $this->perPage,
            'internalPerPage' => $this->internalPerPage,
        ]);
        if ($this->perPage === null) {
            $this->perPage = static::defaultPerPage();
        } else {
            $this->perPage = $this->resolvePerPage();
        }

        $this->internalPerPage = $this->perPage;
    }

    final public function queryStringHasTablePagination(): array
    {
        return [
            'perPage' => ['except' => static::defaultPerPage()],
        ];
    }

    final public function bootedHasTablePagination(): void
    {
        // $this->syncInput('perPage', $this->internalPerPage);
        $this->perPage = $this->internalPerPage;
    }

    public function hydratedHasTablePagination(): void
    {
        // $this->syncInput('perPage', $this->internalPerPage);
        $this->perPage = $this->resolvePerPage();
    }

    final public function setPerPage(int $perPage): void
    {
        if (! in_array($perPage, static::perPageOptions(), true)) {
            return;
        }

        $this->perPage = $perPage;

        $this->internalPerPage = $this->perPage;

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
        if ($this->internalPerPage !== null) {
            return $this->internalPerPage;
        }

        return $this->perPage;
    }
}
