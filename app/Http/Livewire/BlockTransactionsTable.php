<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\HasLazyLoadingPagination;
use App\Models\Block;
use App\ViewModels\BlockViewModel;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

/** @property Collection $lazyLoadedData */
final class BlockTransactionsTable extends Component
{
    use DeferLoading;
    use HasLazyLoadingPagination;

    public string $blockId;

    /** @var mixed */
    protected $listeners = ['currencyChanged' => '$refresh'];

    public function mount(BlockViewModel $block): void
    {
        $this->totalCount = $block->transactionCount();
        $this->blockId    = $block->id();
    }

    public function getBlock(): Block
    {
        return Block::findOrFail($this->blockId);
    }

    public function render(): View
    {
        return view('livewire.block-transactions-table', [
            'transactions' => ViewModelFactory::collection($this->lazyLoadedData),
        ]);
    }

    public function getLazyLoadedDataProperty(): Collection
    {
        if (! $this->isReady) {
            return new Collection();
        }

        return $this->getBlock()
            ->transactions()
            ->limit($this->perPage * $this->page)
            ->get();
    }
}
