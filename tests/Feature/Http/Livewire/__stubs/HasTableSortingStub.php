<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Livewire\__stubs;

use App\Enums\SortDirection;
use App\Http\Livewire\Concerns\HasTableSorting;
use Livewire\Component;

/**
 * @coversNothing
 */
class HasTableSortingStub extends Component
{
    use HasTableSorting;

    public const INITIAL_SORT_KEY = 'age';

    public const INITIAL_SORT_DIRECTION = SortDirection::DESC;
}
