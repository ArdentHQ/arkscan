<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Network;
use App\Services\CryptoCompare;
use App\Services\Settings;
use Livewire\Component;

final class PriceTicker extends Component
{
    public function render()
    {
        return view('livewire.price-ticker', [
            'from'  => Network::currency(),
            'to'    => Settings::currency(),
            'price' => CryptoCompare::price(Network::currency(), Settings::currency()),
        ]);
    }
}
