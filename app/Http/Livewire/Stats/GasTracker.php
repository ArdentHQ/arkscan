<?php

declare(strict_types=1);

namespace App\Http\Livewire\Stats;

use App\Facades\Services\GasTracker as GasTrackerFacade;
use App\Services\MainsailApi;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class GasTracker extends Component
{
    public function render(): View
    {
        return view('livewire.stats.gas-tracker', [
            'lowFee' => [
                'amount' => GasTrackerFacade::low(),
                'duration' => MainsailApi::timeToForge(),
            ],
            'averageFee' => [
                'amount' => GasTrackerFacade::average(),
                'duration' => MainsailApi::timeToForge(),
            ],
            'highFee' => [
                'amount' => GasTrackerFacade::high(),
                'duration' => MainsailApi::timeToForge(),
            ],
        ]);
    }
}
