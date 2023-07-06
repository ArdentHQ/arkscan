<?php

declare(strict_types=1);

namespace App\Http\Livewire\Modals;

use App\ViewModels\WalletViewModel;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class ExportTransactions extends Component
{
    use HasModal;

    public string $address;

    public function mount(WalletViewModel $wallet): void
    {
        $this->address = $wallet->address();
    }

    public function render(): View
    {
        return view('livewire.modals.export-transactions');
    }
}
