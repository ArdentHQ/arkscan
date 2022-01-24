<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Wallets;
use App\Models\Wallet;
use App\ViewModels\WalletViewModel;
use Illuminate\View\View;
use Livewire\Component;

final class WalletBalance extends Component
{
    public string $walletAddress;

    /** @var mixed */
    protected $listeners = [
        'currencyChanged' => '$refresh',
    ];

    public function mount(Wallet $wallet): void
    {
        $this->walletAddress = $wallet->address;
    }

    public function render(): View
    {
        return view('livewire.wallet-balance', [
            'balance' => $this->getWalletView()->balanceFiat(),
        ]);
    }

    private function getWallet(): Wallet
    {
        return Wallets::findByAddress($this->walletAddress);
    }

    private function getWalletView(): WalletViewModel
    {
        return new WalletViewModel($this->getWallet());
    }
}
