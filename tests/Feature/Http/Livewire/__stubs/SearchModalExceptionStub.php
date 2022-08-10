<?php

namespace Tests\Feature\Http\Livewire\__stubs;

use App\Http\Livewire\Concerns\ManagesSearch;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use App\Http\Livewire\Concerns\HandlesSearchModal;
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
