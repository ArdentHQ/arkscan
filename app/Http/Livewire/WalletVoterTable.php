<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Concerns\HasTablePagination;
use App\Models\Scopes\OrderByBalanceScope;
use App\Models\Wallet;
use App\ViewModels\ViewModelFactory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Component;

/** @property LengthAwarePaginator $wallets */
final class WalletVoterTable extends Component
{
    use HasTablePagination;

    public const PER_PAGE = 10;

    public string $publicKey;

    public string $username;

    /** @var mixed */
    protected $listeners = ['currencyChanged' => '$refresh'];

    public function mount(string $publicKey, string $username): void
    {
        $this->publicKey = $publicKey;
        $this->username  = $username;
    }

    public function render(): View
    {
        return view('livewire.wallet-voter-table', [
            'wallets' => ViewModelFactory::paginate($this->wallets),
        ]);
    }

    public function getNoResultsMessageProperty(): ?string
    {
        if ($this->wallets->total() === 0) {
            return trans('tables.wallets.no_results');
        }

        return null;
    }

    public function getWalletsProperty(): LengthAwarePaginator
    {
        return Wallet::where('attributes->vote', $this->publicKey)
            ->withScope(OrderByBalanceScope::class)
            ->paginate($this->perPage);
    }
}
