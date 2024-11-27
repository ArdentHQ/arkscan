<?php

declare(strict_types=1);

namespace App\Http\Livewire\Stats;

use App\Facades\Services\GasTracker as GasTrackerFacade;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class GasTracker extends Component
{
    public function render(): View
    {
        return view('livewire.stats.gas-tracker', [
            'fees' => [
                'low'     => GasTrackerFacade::low(),
                'average' => GasTrackerFacade::average(),
                'high'    => GasTrackerFacade::high(),
            ],
        ]);
    }
}
