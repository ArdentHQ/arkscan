<?php

declare(strict_types=1);

namespace App\Http\Livewire\Home;

use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\HasTablePagination;
use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

/**
 * @property LengthAwarePaginator $blocks
 * */
final class Blocks extends Component
{
    use DeferLoading;
    use HasTablePagination;

    /** @var mixed */
    protected $listeners = [
        'setBlocksReady'       => 'setIsReady',
        'currencyChanged'      => '$refresh',
        'echo:blocks,NewBlock' => '$refresh',
    ];

    public function render(): View
    {
        return view('livewire.home.blocks', [
            'blocks' => ViewModelFactory::paginate($this->blocks),
        ]);
    }

    public function getNoResultsMessageProperty(): ?string
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

        $page = $this->page ?? 1;

        $total = Cache::remember('blocks_total_count', 60, fn () => Block::query()->count());

        $blocks = Block::withScope(OrderByHeightScope::class)
            ->with('transactions')
            ->forPage($page, $this->perPage)
            ->get();

        return new LengthAwarePaginator($blocks, $total, $this->perPage, $page);
    }
}
