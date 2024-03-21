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

    public string $publicKey;

    public ?string $username = null;

    public bool $hasForgedBlocks = false;

    public function mount(WalletViewModel $wallet): void
    {
        /** @var string $publicKey */
        $publicKey = $wallet->publicKey();

        $this->publicKey = $publicKey;

        if ($wallet->isValidator()) {
            $this->username = $wallet->username();
        }
    }

    public function render(): View
    {
        return view('livewire.modals.export-blocks');
    }

    public function setIsReady(): void
    {
        $wallet = Wallets::findByPublicKey($this->publicKey);

        $this->hasForgedBlocks = $wallet->blocks()->count() > 0;
    }
}
