<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Block;
use App\ViewModels\ViewModelFactory;
use Illuminate\View\View;
use Livewire\Component;

final class LatestBlocksTable extends Component
{
    public function render(): View
    {
        return view('livewire.latest-blocks-table', [
            'blocks' => ViewModelFactory::collection(Block::latestByHeight()->take(15)->get()),
        ]);
    }
}
