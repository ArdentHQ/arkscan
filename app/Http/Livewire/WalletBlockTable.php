<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\View\View;
use Livewire\Component;

final class WalletBlockTable extends Component
{
    use HasPagination;

    public string $publicKey;

    public string $username;

    public function mount(string $publicKey, string $username): void
    {
        $this->publicKey = $publicKey;
        $this->username  = $username;
    }

    public function render(): View
    {
        return view('livewire.wallet-block-table', [
            'blocks' => ViewModelFactory::paginate(Block::where('generator_public_key', $this->publicKey)->withScope(OrderByHeightScope::class)->paginate()),
        ]);
    }
}
