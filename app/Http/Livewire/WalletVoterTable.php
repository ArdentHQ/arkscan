<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Wallet;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\View\View;
use Livewire\Component;

final class WalletVoterTable extends Component
{
    use HasPagination;

    public string $publicKey;

    public function mount(string $publicKey): void
    {
        $this->publicKey = $publicKey;
    }

    public function render(): View
    {
        return view('livewire.wallet-voter-table', [
            'wallets' => ViewModelFactory::paginate(Wallet::where('attributes->vote', $this->publicKey)->wealthy()->paginate()),
        ]);
    }
}
