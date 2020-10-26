<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Concerns\HasDelegateQueries;
use Illuminate\View\View;
use Livewire\Component;

final class MonitorDelegateTabs extends Component
{
    use HasDelegateQueries;

    public function render(): View
    {
        return view('livewire.monitor-delegate-tabs', [
            'countActive'   => $this->activeQuery()->count(),
            'countStandby'  => $this->standbyQuery()->count(),
            'countResigned' => $this->resignedQuery()->count(),
        ]);
    }
}
