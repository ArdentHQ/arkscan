<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Network;
use App\Models\Block;
use App\ViewModels\ViewModelFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Livewire\Component;

final class LatestBlocksTable extends Component
{
    private Collection $blocks;

    public function mount(): void
    {
        $this->blocks = new Collection();
    }

    public function render(): View
    {
        return view('livewire.latest-blocks-table', [
            'blocks' => ViewModelFactory::collection($this->blocks),
        ]);
    }

    public function pollBlocks(): void
    {
        $this->blocks = Cache::remember(
            'latestBlocksTable',
            Network::blockTime(),
            fn () => Block::latestByHeight()->take(15)->get()
        );
    }
}
