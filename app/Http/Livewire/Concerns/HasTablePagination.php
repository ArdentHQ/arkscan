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
        $this->perPage = $this->getDefault();
    }

    public function getQueryString(): array
    {
        return [
            'perPage' => ['except' => $this->getDefault()],
        ];
    }

    public function setPerPage(int $perPage): void
    {
        if (! in_array($perPage, trans('pagination.per_page_options'), true)) {
            return;
        }

        $this->perPage = $perPage;
    }

    private function getDefault(): int
    {
        if (defined(static::class.'::PER_PAGE')) {
            $const = constant(static::class.'::PER_PAGE');
            if (is_int($const)) {
                return $const;
            }
        }

        return 10; // @codeCoverageIgnore - we don't have any usage where it falls back to default
    }
}
