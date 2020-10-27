<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Block;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\View\View;
use Livewire\Component;

final class WalletBlockTable extends Component
{
    use HasPagination;

    public string $publicKey;

    public function mount(string $publicKey): void
    {
        $this->publicKey = $publicKey;
    }

    public function render(): View
    {
        return view('livewire.wallet-block-table', [
            'blocks' => ViewModelFactory::paginate(Block::where('generator_public_key', $this->publicKey)->latestByHeight()->paginate()),
        ]);
    }
}
