<?php

declare(strict_types=1);

namespace App\Http\Livewire\Abstracts;

use App\Http\Livewire\Concerns\HasTablePagination;
use Livewire\Component;

abstract class TabbedTableComponent extends Component
{
    use HasTablePagination {
        HasTablePagination::resolvePage as baseResolvePage;
    }

    public function resolvePage()
    {
        if ($this->query('view') === $this->view() || ($this->query('view') === null && $this->isDefaultView())) {
            return $this->baseResolvePage();
        }

        return $this->page;
    }

    abstract protected function view(): string;

    protected function isDefaultView(): bool
    {
        return false;
    }

    private function query(string $key, mixed $default = null): mixed
    {
        if (request()->exists($key)) {
            return request()->query($key);
        }

        $referer = request()->header('Referer');
        if (empty($array)) {
            return $default;
        }

        parse_str((string) parse_url($referer, PHP_URL_QUERY), $refererQueryString);

        if (array_key_exists($key, $refererQueryString)) {
            return $refererQueryString[$key];
        }

        return $default;
    }
}
