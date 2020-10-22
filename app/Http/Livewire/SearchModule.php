<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Concerns\ManagesSearch;
use Livewire\Component;

final class SearchModule extends Component
{
    use ManagesSearch;

    public bool $isSlim = false;

    public bool $isAdvanced = false;

    public function mount(bool $isSlim = false, bool $isAdvanced = false): void
    {
        $this->isSlim     = $isSlim;
        $this->isAdvanced = $isAdvanced;

        $this->restoreState(request('state', []));
    }

    public function performSearch(): void
    {
        $data = $this->validateSearchQuery();

        if ($this->isAdvanced) {
            $this->emit('searchTriggered', $data);
        } else {
            $this->redirectRoute('search', ['state' => $data]);
        }
    }
}
