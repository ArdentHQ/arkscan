<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Wallets;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class WalletTables extends Component
{
    public string $address;

    public string $view = 'transactions';

    /** @var mixed */
    protected $listeners = [
        'showWalletView',
    ];

    public function getQueryString(): array
    {
        return [
            'view' => ['except' => 'transactions'],
        ];
    }

    public function mount(WalletViewModel $wallet): void
    {
        $this->address = $wallet->address();
    }

    public function render(): View
    {
        return view('livewire.wallet-tables', [
            'wallet' => ViewModelFactory::make(Wallets::findByAddress($this->address)),
        ]);
    }

    public function showWalletView(string $view): void
    {
        $this->view = $view;
    }
}
