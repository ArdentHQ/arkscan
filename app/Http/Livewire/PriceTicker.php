<?php

declare(strict_types=1);

namespace  App\Http\Livewire;

use Livewire\Component;

final class PriceTicker extends Component
{
    public function render()
    {
        return view('livewire.price-ticker', [
            'from'  => 'ARK',
            'to'    => 'USD',
            'price' => 7.48,
        ]);
    }
}
