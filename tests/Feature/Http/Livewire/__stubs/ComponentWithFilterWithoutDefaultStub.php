<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Livewire\__stubs;

use App\Http\Livewire\Concerns\HasTableFilter;
use Livewire\Component;

/**
 * @coversNothing
 */
class ComponentWithFilterWithoutDefaultStub extends Component
{
    use HasTableFilter;

    public const INITIAL_FILTERS = [
        'default' => [
            'is_true'   => true,
            'is_false'  => false,
        ],
    ];

    public array $alreadyLoadedViews = [
        'default' => false,
    ];

    public function queryString(): array
    {
        return [
            'filters.default.is_true'  => ['as' => 'is_true', 'except' => true],
            'filters.default.is_false' => ['as' => 'is_false', 'except' => false],
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

    public function mount(): void
    {
        $this->filters = static::defaultFilters();

        foreach (array_keys($this->alreadyLoadedViews) as $view) {
            if (! array_key_exists($view, $this->filters)) {
                continue;
            }

            $this->filters[$view] = $this->resolveFilters($this->filters[$view], 'default');
        }
    }
}
