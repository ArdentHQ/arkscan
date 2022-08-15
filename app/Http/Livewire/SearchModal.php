<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Concerns\HandlesSearchModal;
use App\Http\Livewire\Concerns\ManagesSearch;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Livewire\Component;

final class SearchModal extends Component
{
    use ManagesSearch;
    use HasModal;
    use HandlesSearchModal;

    /** @var mixed */
    protected $listeners = [
        'openSearchModal' => 'openModal',
        'redirectToPage',
    ];
}
