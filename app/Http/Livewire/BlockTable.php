<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\View\View;
use Livewire\Component;

final class BlockTable extends Component
{
    use HasPagination;

    /** @phpstan-ignore-next-line */
    protected $listeners = ['currencyChanged' => '$refresh'];

    public function render(): View
    {
        return view('livewire.block-table', [
            'blocks' => ViewModelFactory::paginate(Block::withScope(OrderByHeightScope::class)->paginate()),
        ]);
    }
}
