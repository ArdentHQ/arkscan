<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Concerns\HasTablePagination;
use App\Models\Scopes\OrderByBalanceScope;
use App\Models\Wallet;
use App\ViewModels\ViewModelFactory;
use Illuminate\View\View;
use Livewire\Component;

final class TopAccountsTable extends Component
{
    use HasTablePagination;

    const PER_PAGE = 25;

    public function render(): View
    {
        return view('livewire.top-accounts-table', [
            'wallets' => ViewModelFactory::paginate(Wallet::withScope(OrderByBalanceScope::class)->paginate($this->perPage)),
        ]);
    }
}
