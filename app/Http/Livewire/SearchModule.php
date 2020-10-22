<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Concerns\ManagesSearch;
use Livewire\Component;

final class SearchModule extends Component
{
    use ManagesSearch;

    public string $hello = 'world';

    public bool $isSlim = false;

    public bool $isAdvanced = false;

    // This prevents a weird bug where Livewire updates the client URL to the internal component URL.
    protected $queryString = ['hello'];

    public function mount(bool $isSlim = false, bool $isAdvanced = false): void
    {
        $this->isSlim     = $isSlim;
        $this->isAdvanced = $isAdvanced;
    }

    public function performSearch(): void
    {
        $data = $this->validateSearchQuery();

        $this->emit('searchTriggered', $data);

        // 1. We found a single match > Redirect to result page
        // 2. We have an advanced search on any other page > Redirect to advanced page
    }
}
