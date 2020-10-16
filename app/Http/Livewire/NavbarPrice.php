<?php

declare(strict_types=1);

namespace  App\Http\Livewire;

use Livewire\Component;

final class NavbarPrice extends Component
{
    public function render()
    {
        return view('livewire.navbar-price', [
            'from'  => 'ARK',
            'to'    => 'USD',
            'price' => 7.48,
        ]);
    }
}
