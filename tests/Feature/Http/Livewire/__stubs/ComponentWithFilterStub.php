<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Livewire\__stubs;

use App\Http\Livewire\Concerns\HasTableFilter;
use Livewire\Component;

/**
 * @coversNothing
 */
class ComponentWithFilterStub extends Component
{
    use HasTableFilter;

    public const INITIAL_FILTERS = [
        'testing' => [
            'is_true'   => true,
            'is_false'  => false,
        ],
    ];

    public function queryString(): array
    {
        return [
            'filters.testing.is_true'  => ['as' => 'is_true', 'except' => true],
            'filters.testing.is_false' => ['as' => 'is_false', 'except' => false],
        ];
    }

    public function render()
    {
        return '<div></div>';
    }

    public function getNoResultsMessageProperty(): null|string
    {
        return 'No results found';
    }
}
