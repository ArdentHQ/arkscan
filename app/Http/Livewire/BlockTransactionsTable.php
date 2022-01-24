<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Block;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class BlockTransactionsTable extends Component
{
    use HasPagination;

    public string $blockId;

    /** @var mixed */
    protected $listeners = ['currencyChanged' => '$refresh'];

    public function mount(string $blockId): void
    {
        $this->blockId = $blockId;
    }

    public function getBlock(): Block
    {
        return Block::findOrFail($this->blockId);
    }

    public function render(): View
    {
        return view('livewire.transaction-table', [
            'transactions' => ViewModelFactory::paginate($this->getBlock()->transactions()->paginate(25)),
        ]);
    }
}
