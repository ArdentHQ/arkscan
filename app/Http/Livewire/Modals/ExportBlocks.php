<?php

declare(strict_types=1);

namespace App\Http\Livewire\Modals;

use App\ViewModels\WalletViewModel;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class ExportBlocks extends Component
{
    use HasModal;

    public string $publicKey;

    public string $username;

    public function mount(WalletViewModel $wallet): void
    {
        $this->publicKey = $wallet->publicKey();
        $this->username  = $wallet->username();
    }

    public function render(): View
    {
        return view('livewire.modals.export-blocks');
    }
}
