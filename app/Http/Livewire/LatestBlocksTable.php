<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Block;
use App\Services\Cache\TableCache;
use App\ViewModels\ViewModelFactory;
use Illuminate\Support\Collection;
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
        $this->blocks = (new TableCache())->setLatestBlocks(fn () => Block::latestByHeight()->take(15)->get());
    }
}
