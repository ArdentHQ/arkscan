<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Livewire\Component;

final class SearchResults extends Component
{
    public array $state = ['type' => null];

    protected $listeners = ['searchTriggered'];

    public function searchTriggered(array $data)
    {
        $this->state['type'] = $data['type'];

        if ($data['type'] === 'block') {
            $this->emit('searchBlocks', $data);
        }

        if ($data['type'] === 'transaction') {
            $this->emit('searchTransactions', $data);
        }

        if ($data['type'] === 'wallet') {
            $this->emit('searchWallets', $data);
        }
    }
}
