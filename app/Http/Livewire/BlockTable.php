<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Block;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\UserInterface\Http\Livewire\Concerns\HasPagination;
use Livewire\Component;

final class BlockTable extends Component
{
    use HasPagination;

    public bool $viewMore = false;

    public function mount(bool $viewMore = false)
    {
        $this->viewMore = $viewMore;
    }

    public function render()
    {
        return view('livewire.block-table', [
            'blocks' => ViewModelFactory::paginate(Block::latestByHeight()->paginate()),
        ]);
    }
}
