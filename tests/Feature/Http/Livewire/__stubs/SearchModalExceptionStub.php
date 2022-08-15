<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Livewire\__stubs;

use App\Http\Livewire\Concerns\HandlesSearchModal;
use App\Http\Livewire\Concerns\ManagesSearch;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Livewire\Component;

/**
 * @coversNothing
 */
class SearchModalExceptionStub extends Component
{
    use ManagesSearch;
    use HasModal;
    use HandlesSearchModal;
}
