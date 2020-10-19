<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Block;
use App\ViewModels\ViewModelFactory;
use Livewire\Component;

final class BlockTable extends Component
{
    public function render()
    {
        return view('livewire.block-table', [
            'blocks' => ViewModelFactory::paginate(Block::latestByHeight()->paginate()),
        ]);
    }
}
