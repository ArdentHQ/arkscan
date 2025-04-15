<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Concerns\HasLazyLoadingPagination;
use App\Models\Block;
use App\Models\Scopes\OrderByTimestampScope;
use App\ViewModels\BlockViewModel;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

/** @property Collection $lazyLoadedData */
final class BlockTransactionsTable extends Component
{
    use HasLazyLoadingPagination;

    public string $blockHash;

    /** @var mixed */
    protected $listeners = ['currencyChanged' => '$refresh'];

    public function mount(BlockViewModel $block): void
    {
        $this->totalCount   = $block->transactionCount();
        $this->blockHash    = $block->hash();
    }

    public function getBlock(): Block
    {
        return Block::where('hash', $this->blockHash)->firstOrFail();
    }

    public function render(): View
    {
        return view('livewire.block-transactions-table', [
            'transactions' => ViewModelFactory::collection($this->lazyLoadedData),
        ]);
    }

    public function getLazyLoadedDataProperty(): Collection
    {
        return $this->getBlock()
            ->transactions()
            ->withScope(OrderByTimestampScope::class)
            ->limit($this->perPage * $this->getPage())
            ->get();
    }
}
