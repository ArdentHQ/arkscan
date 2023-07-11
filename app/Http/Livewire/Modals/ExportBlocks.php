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

    public ?string $publicKey;

    public ?string $username = null;

    public bool $hasForgedBlocks = false;

    public function mount(WalletViewModel $wallet): void
    {
        $this->publicKey = $wallet->publicKey();

        if ($wallet->isDelegate()) {
            $this->username        = $wallet->username();
            $this->hasForgedBlocks = $wallet->blocksCount() > 0;
        }
    }

    public function render(): View
    {
        return view('livewire.modals.export-blocks');
    }
}
