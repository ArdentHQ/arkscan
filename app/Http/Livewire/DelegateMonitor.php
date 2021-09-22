<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Rounds;
use App\Http\Livewire\Concerns\DelegateData;
use Illuminate\View\View;
use Livewire\Component;
use Throwable;

final class DelegateMonitor extends Component
{
    use DelegateData;

    private array $delegates = [];

    public function render(): View
    {
        return view('livewire.delegate-monitor', [
            'delegates'  => $this->delegates,
            'round'      => Rounds::current(),
        ]);
    }

    public function pollDelegates(): void
    {
        // $tracking = DelegateTracker::execute(Rounds::allByRound(112168));

        try {
            $this->delegates = $this->fetchDelegates();
            // @codeCoverageIgnoreStart
        } catch (Throwable) {
            // @README: If any errors occur we want to keep polling until we have a list of delegates
            $this->pollDelegates();
        }
        // @codeCoverageIgnoreEnd
    }
}
