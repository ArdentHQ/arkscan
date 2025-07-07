<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Abstracts\TabbedTableComponent;
use App\Models\Block;
use App\Models\Scopes\OrderByTimestampScope;
use App\ViewModels\ViewModelFactory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

/**
 * @property LengthAwarePaginator $blocks
 * */
final class BlockTable extends TabbedTableComponent
{
    /** @var mixed */
    protected $listeners = [
        'currencyChanged'      => '$refresh',
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

        /** @var Block $firstBlock */
        $firstBlock = Block::withScope(OrderByTimestampScope::class, 'asc')->first();

        $lastBlockHeight = $lastBlock->number->toNumber();
        $blockCount      = $lastBlockHeight;

        $firstBlockHeight = $firstBlock->number->toNumber();
        if ($firstBlockHeight > 1) {
            $blockCount -= $firstBlockHeight - 1; // Adjust for the first block if it's not the genesis block
        }

        $heightTo   = $lastBlockHeight - ($this->perPage * ($this->getPage() - 1));
        $heightFrom = $heightTo - $this->perPage;

        $blocks = Block::withScope(OrderByTimestampScope::class)
            ->where('number', '<=', $heightTo)
            ->where('number', '>', $heightFrom)
            ->get();

        return new LengthAwarePaginator($blocks, $blockCount, $this->perPage, $this->getPage(), [
            'path'     => route('blocks'),
            'pageName' => 'page',
        ]);
    }
}
