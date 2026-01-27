<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\HasTablePagination;
use App\Models\Scopes\OrderByBalanceScope;
use App\Models\Wallet;
use App\ViewModels\WalletViewModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Component;

/**
 * @property LengthAwarePaginator $wallets
 * */
final class TopAccountsTable extends Component
{
    use DeferLoading;
    use HasTablePagination;

    public const PER_PAGE = 25;

    /** @var mixed */
    protected $listeners = [
        'currencyChanged' => '$refresh',
    ];

    public function render(): View
    {
        return view('livewire.top-accounts-table', [
            'wallets' => $this->wallets->through(fn (Wallet $wallet) => new WalletViewModel($wallet)),
        ]);
    }

    public function getWalletsProperty(): LengthAwarePaginator
    {
        if (! $this->isReady) {
            return new LengthAwarePaginator([], 0, $this->perPage);
        }

        return Wallet::withScope(OrderByBalanceScope::class)
            ->paginate($this->perPage);
    }
}
