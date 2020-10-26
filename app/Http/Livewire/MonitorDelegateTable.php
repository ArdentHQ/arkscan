<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Concerns\HasDelegateQueries;
use App\ViewModels\ViewModelFactory;
use Illuminate\View\View;
use Livewire\Component;

final class MonitorDelegateTable extends Component
{
    use HasDelegateQueries;

    public array $state = [
        'status' => 'active',
    ];

    /** @phpstan-ignore-next-line */
    protected $listeners = ['filterByDelegateStatus'];

    public function filterByDelegateStatus(string $value): void
    {
        $this->state['type'] = $value;
    }

    public function render(): View
    {
        if ($this->state['status'] === 'resigned') {
            $delegates = $this->resignedQuery()->get();
        } elseif ($this->state['status'] === 'standby') {
            $delegates = $this->standbyQuery()->get();
        } else {
            $delegates = $this->activeQuery()->get();
        }

        return view('livewire.monitor-delegate-table', [
            'delegates' => ViewModelFactory::collection($delegates),
        ]);
    }
}
