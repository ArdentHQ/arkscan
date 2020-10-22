<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Wallet;
use App\Services\Search\WalletSearch;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Component;

final class WalletTable extends Component
{
    use HasPagination;

    protected ?LengthAwarePaginator $wallets = null;

    protected $listeners = ['searchWallets'];

    public function mount(bool $viewMore = false): void
    {
        $this->viewMore = $viewMore;
    }

    public function searchWallets(array $data): void
    {
        $this->wallets = (new WalletSearch())->search($data)->paginate();
    }

    public function render(): View
    {
        if (is_null($this->wallets)) {
            $this->wallets = Wallet::wealthy()->paginate();
        }

        return view('livewire.wallet-table', [
            'wallets' => ViewModelFactory::paginate($this->wallets),
        ]);
    }
}
