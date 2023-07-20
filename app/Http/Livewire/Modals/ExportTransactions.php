<?php

declare(strict_types=1);

namespace App\Http\Livewire\Modals;

use App\Facades\Wallets;
use App\Http\Livewire\Concerns\DeferLoading;
use App\ViewModels\WalletViewModel;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class ExportTransactions extends Component
{
    use DeferLoading;
    use HasModal;

    public string $address;

    public bool $hasTransactions = false;

    public function mount(WalletViewModel $wallet): void
    {
        $this->address = $wallet->address();
    }

    public function render(): View
    {
        return view('livewire.modals.export-transactions');
    }

    public function setIsReady(): void
    {
        $wallet = Wallets::findByAddress($this->address);

        if ($wallet->receivedTransactions()->count() > 0) {
            $this->hasTransactions = true;
        } elseif ($wallet->sentTransactions()->count() > 0) {
            $this->hasTransactions = true;
        }
    }
}
