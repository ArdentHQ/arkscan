<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Livewire\__stubs;

use App\Http\Livewire\Concerns\HasTableFilter;
use Livewire\Component;

/**
 * @coversNothing
 */
class HasTableFilterStub extends Component
{
    use HasTableFilter;

    const STUB_INITIAL_FILTERS = [
        'testing' => true,
    ];

    public array $pages = [];

    public function getNoResultsMessageProperty(): null|string
    {
        return 'No results found';
    }

    public function gotoPage(int $page, string $name = 'default'): void
    {
        $this->pages[$name] = $page;
    }
}
