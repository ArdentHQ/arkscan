<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Network;
use App\Models\Wallet;
use App\ViewModels\ViewModelFactory;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

final class DelegateActiveTable extends Component
{
    public bool $load = false;

    /**
     * @var mixed
     */
    protected $listeners = ['tabFiltered'];

    public function render(): View
    {
        return view('livewire.delegate-active-table', [
            'delegates' => $this->load ? $this->delegates() : [],
        ]);
    }

    public function tabFiltered(string $tab): void
    {
        $this->load = $tab === 'active';
    }

    private function delegates(): Collection
    {
        return ViewModelFactory::collection(
            Wallet::query()
                ->whereNotNull('attributes->delegate->username')
                ->whereRaw("(\"attributes\"->'delegate'->>'rank')::numeric <= ?", [Network::delegateCount()])
                ->orderByRaw("(\"attributes\"->'delegate'->>'rank')::numeric ASC")
                ->get()
        );
    }
}
