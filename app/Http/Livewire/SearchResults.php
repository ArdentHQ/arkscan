<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Services\Search\BlockSearch;
use App\Services\Search\TransactionSearch;
use App\Services\Search\WalletSearch;
use Livewire\Component;

final class SearchResults extends Component
{
    public array $state = [
        'type'    => null,
        'results' => null,
    ];

    /** @phpstan-ignore-next-line */
    protected $listeners = ['searchTriggered'];

    public function searchTriggered(array $data): void
    {
        $this->state['type'] = $data['type'];

        if ($data['type'] === 'block') {
            $this->state['results'] = (new BlockSearch())->search($data);
        }

        if ($data['type'] === 'transaction') {
            $this->state['results'] = (new TransactionSearch())->search($data);
        }

        if ($data['type'] === 'wallet') {
            $this->state['results'] = (new WalletSearch())->search($data);
        }
    }
}
