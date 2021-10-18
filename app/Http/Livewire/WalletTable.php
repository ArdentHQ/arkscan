<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Scopes\OrderByBalanceScope;
use App\Models\Wallet;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\View\View;
use Livewire\Component;

final class WalletTable extends Component
{
    use HasPagination;

    public function render(): View
    {
        return view('livewire.wallet-table', [
            'wallets' => ViewModelFactory::paginate(Wallet::withScope(OrderByBalanceScope::class)->paginate()),
        ]);
    }
}
