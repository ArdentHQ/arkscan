<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Network;
use App\Models\Wallet;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

final class DelegateStandbyTable extends Component
{
    use WithPagination;

    public bool $load = false;

    /**
     * @var mixed
     */
    protected $listeners = ['tabFiltered'];

    public function render(): View
    {
        return view('livewire.delegate-standby-table', [
            'delegates' => $this->load ? $this->delegates() : [],
        ]);
    }

    public function tabFiltered(string $tab): void
    {
        $this->gotoPage(1);

        $this->load = $tab === 'standby';
    }

    private function delegates(): LengthAwarePaginator
    {
        return ViewModelFactory::paginate(
            Wallet::query()
                ->whereNotNull('attributes->delegate->username')
                ->whereRaw("(\"attributes\"->'delegate'->>'rank')::numeric > ?", [Network::delegateCount()])
                ->orderByRaw("(\"attributes\"->'delegate'->>'rank')::numeric ASC")
                ->paginate(Network::delegateCount())
        );
    }
}
