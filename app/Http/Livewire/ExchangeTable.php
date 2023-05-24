<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Exchange;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

final class ExchangeTable extends Component
{
    public function render(): View
    {
        return view('livewire.exchange-table', [
            'exchanges' => $this->getExchanges(),
        ]);
    }

    public function getExchanges(): Collection
    {
        return Exchange::orderBy('name')->get();
    }
}
