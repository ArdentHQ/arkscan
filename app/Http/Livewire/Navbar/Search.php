<?php

declare(strict_types=1);

namespace App\Http\Livewire\Navbar;

use App\Http\Livewire\Concerns\ManagesSearch;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Search extends Component
{
    use HasModal;
    use ManagesSearch;

    /** @var mixed */
    protected $listeners = [
        'openSearchModal' => 'openModal',
    ];

    public function render(): View
    {
        $results = $this->results();

        return view('livewire.navbar.search', [
            'results'    => $results,
            'hasResults' => $results !== null && $results->isNotEmpty(),
        ]);
    }
}
