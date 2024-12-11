<?php

declare(strict_types=1);

namespace App\Http\Livewire\Modals;

use App\Facades\Wallets;
use App\Http\Livewire\Concerns\DeferLoading;
use App\ViewModels\WalletViewModel;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class ExportBlocks extends Component
{
    use DeferLoading;
    use HasModal;

    public string $address;

    public bool $hasForgedBlocks = false;

    public function mount(WalletViewModel $wallet): void
    {
        $this->address = $wallet->address();
    }

    public function render(): View
    {
        return view('livewire.modals.export-blocks');
    }

    public function setIsReady(): void
    {
        $wallet = Wallets::findByAddress($this->address);

        $this->hasForgedBlocks = $wallet->blocks()->count() > 0;
    }
}
