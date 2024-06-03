<?php

declare(strict_types=1);

namespace App\Http\Livewire\Abstracts;

use App\Http\Livewire\Concerns\HasTablePagination;
use Livewire\Component;
use Livewire\Mechanisms\HandleRequests\HandleRequests;

abstract class TabbedTableComponent extends Component
{
    use HasTablePagination;

    final public function resolvePage(): int
    {
        if (! app(HandleRequests::class)->isLivewireRequest()) {
            return $this->getPage();
        }

        return (int) $this->query('page', $this->getPage());
    }

    final public function resolvePerPage(): int
    {
        return (int) $this->query('perPage', static::defaultPerPage());
    }

    private function query(string $key, mixed $default = null): mixed
    {
        /** @var string|null $referer */
        $referer = request()->header('Referer');

        if ($referer === null) {
            return $default;
        }

        if (strlen($referer) === 0) {
            return $default;
        }

        parse_str((string) parse_url($referer, PHP_URL_QUERY), $refererQueryString);

        if (array_key_exists($key, $refererQueryString)) {
            return $refererQueryString[$key];
        }

        return $default;
    }
}
