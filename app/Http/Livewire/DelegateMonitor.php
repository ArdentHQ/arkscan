<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Rounds;
use App\Http\Livewire\Concerns\DelegateData;
use Illuminate\Support\Facades\Cache;
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

            Cache::forget('poll-delegates-exception-occurrence');

        } catch (Throwable $e) {
            $occurrences = Cache::increment('poll-delegates-exception-occurrence');

            if ($occurrences >= 3) {
                throw $e;
            }

            // @README: If any errors occur we want to keep polling until we have a list of delegates
            $this->pollDelegates();
        }
    }
}
