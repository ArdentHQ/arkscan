<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\HasTablePagination;
use App\Models\Block;
use App\Models\Scopes\OrderByTimestampScope;
use App\ViewModels\ViewModelFactory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Component;

/**
 * @property LengthAwarePaginator $blocks
 * */
final class BlockTable extends Component
{
    use DeferLoading;
    use HasTablePagination;

    /** @var mixed */
    protected $listeners = [
        'currencyChanged' => '$refresh',
        'echo:blocks,NewBlock' => '$refresh',
    ];

    public function render(): View
    {
        return view('livewire.block-table', [
            'blocks' => ViewModelFactory::paginate($this->blocks),
        ]);
    }

    public function getNoResultsMessageProperty(): null|string
    {
        if ($this->blocks->total() === 0) {
            return trans('tables.blocks.no_results');
        }

        return null;
    }

    public function getBlocksProperty(): LengthAwarePaginator
    {
        if (! $this->isReady) {
            return new LengthAwarePaginator([], 0, $this->perPage);
        }

        /** @var Block $lastBlock */
        $lastBlock = Block::withScope(OrderByTimestampScope::class)->first();

        $lastBlockHeight = $lastBlock->height->toNumber();
        $heightTo        = $lastBlockHeight - ($this->perPage * ($this->page - 1));
        $heightFrom      = $heightTo - $this->perPage;

        $blocks = Block::withScope(OrderByTimestampScope::class)
            ->where('height', '<=', $heightTo)
            ->where('height', '>', $heightFrom)
            ->get();

        return new LengthAwarePaginator($blocks, $lastBlockHeight, $this->perPage, $this->page, [
            'path'     => route('blocks'),
            'pageName' => 'page',
        ]);
    }
}
