<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\Support\Arr;

/** @property int $perPage */
trait HasTablePagination
{
    use HasPagination;
    use NormalizesConstantPrefixes;
    use WithHooks;

    public array $paginatorsPerPage = [];

    public function queryStringHasTablePagination(): array
    {
        $queryString = [
            'paginatorsPerPage.default' => ['as' => 'per-page', 'except' => static::defaultPerPage()],
        ];

        return $queryString;
    }

    final public function getPerPage(string $name = 'default'): int
    {
        return (int) Arr::get($this->paginatorsPerPage, $name, static::defaultPerPage($name));
    }

    final public function setPerPage(int $perPage, string $name = 'default'): void
    {
        if (! in_array($perPage, static::perPageOptions(), true)) {
            return;
        }

        if ($this->getPerPage($name) === $perPage) {
            return;
        }

        $this->setWithHooks('paginatorsPerPage', $perPage, $name);

        if ($name === 'default') {
            $this->gotoPage(1);
        } else {
            $this->gotoPage(1, name: $name);
        }
    }

    public function getPerPageProperty(): int
    {
        return $this->getPerPage();
    }

    public static function perPageOptions(): array
    {
        return trans('pagination.per_page_options');
    }

    final public static function defaultPerPage(string $prefix = ''): int
    {
        $prefix = self::normalizePrefix($prefix, 'default');

        if (defined(static::class.'::'.$prefix.'PER_PAGE')) {
            $const = constant(static::class.'::'.$prefix.'PER_PAGE');
            if (is_int($const)) {
                return $const;
            }
        }

        return (int) config('arkscan.pagination.per_page');
    }

    protected function resolvePerPage(?int $default = null): int
    {
        if (request()->exists('per-page') && ! is_numeric(request()->query('per-page'))) {
            return static::defaultPerPage();
        }

        return (int) request()->query('per-page', $default ?? $this->getPerPage());
    }
}
