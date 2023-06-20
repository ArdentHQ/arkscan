<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Concerns\HasTablePagination;
use App\Models\Scopes\OrderByBalanceScope;
use App\Models\Wallet;
use App\ViewModels\ViewModelFactory;
use Illuminate\View\View;
use Livewire\Component;

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
            'wallets' => ViewModelFactory::paginate(Wallet::where('attributes->vote', $this->publicKey)
                ->withScope(OrderByBalanceScope::class)
                ->paginate($this->perPage)),
        ]);
    }
}
