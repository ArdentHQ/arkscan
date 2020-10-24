<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Block;
use App\ViewModels\ViewModelFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Livewire\Component;

final class LatestBlocksTable extends Component
{
    public function render(): View
    {
        $blocks = Cache::remember(
            'latestBlocksTable',
            8,
            fn () => Block::latestByHeight()->take(15)->get()
        );

        return view('livewire.latest-blocks-table', [
            'blocks' => ViewModelFactory::collection($blocks),
        ]);
    }
}
