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

final class DelegateResignedTable extends Component
{
    use WithPagination;

    public bool $load = false;

    /**
     * @var mixed
     */
    protected $listeners = ['tabFiltered'];

    public function render(): View
    {
        return view('livewire.delegate-resigned-table', [
            'delegates' => $this->load ? $this->delegates() : [],
        ]);
    }

    public function tabFiltered(string $tab): void
    {
        $this->gotoPage(1);

        $this->load = $tab === 'resigned';
    }

    private function delegates(): LengthAwarePaginator
    {
        return ViewModelFactory::paginate(
            Wallet::query()
                ->whereNotNull('attributes->delegate->username')
                ->where('attributes->delegate->resigned', true)
                ->paginate(Network::delegateCount())
        );
    }
}
