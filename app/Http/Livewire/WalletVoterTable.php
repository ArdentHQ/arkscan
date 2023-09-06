<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Abstracts\TabbedTableComponent;
use App\Http\Livewire\Concerns\DeferLoading;
use App\Models\Scopes\OrderByBalanceScope;
use App\Models\Wallet;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

/** @property LengthAwarePaginator $wallets */
final class WalletVoterTable extends TabbedTableComponent
{
    use DeferLoading;

    public string $publicKey;

    /** @var mixed */
    protected $listeners = [
        'setVotersReady'  => 'setIsReady',
        'currencyChanged' => '$refresh',
    ];

    public function mount(WalletViewModel $wallet): void
    {
        /** @var string $publicKey */
        $publicKey = $wallet->publicKey();

        $this->publicKey = $publicKey;
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
        if (! $this->isReady) {
            return new LengthAwarePaginator([], 0, $this->perPage);
        }

        return Wallet::where('attributes->vote', $this->publicKey)
            ->withScope(OrderByBalanceScope::class)
            ->paginate($this->perPage);
    }
}
