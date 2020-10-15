<?php

namespace App\Http\Livewire;

use Livewire\Component;

class NetworkStatusBlock extends Component
{
    public function render()
    {
        return view('livewire.network-status-block', [
            'height'    => '12,198,189',
            'network'   => 'ARK Public Network',
            'supply'    => 'Ѧ 149,245,180',
            'marketCap' => '$ 28,774,470.70',
        ]);
    }
}
