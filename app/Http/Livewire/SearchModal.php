<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Concerns\ManagesSearch;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Illuminate\View\View;
use Livewire\Component;

final class SearchModal extends Component
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

        return view('livewire.search-modal', [
            'results'    => $results,
            'hasResults' => $results->isNotEmpty(),
        ]);
    }

    public function closeModal(): void
    {
        $this->modalShown = false;

        $this->clear();

        $this->modalClosed();
    }
}
