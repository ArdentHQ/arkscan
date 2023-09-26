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
use Livewire\Component;

/**
 * @property Blocks $blocks
 * */
final class Blocks extends Component
{
    use DeferLoading;
    use HasTablePagination;

    /** @var mixed */
    protected $listeners = [
        'setBlocksReady'  => 'setIsReady',
        'currencyChanged' => '$refresh',
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

        return Block::withScope(OrderByHeightScope::class)
            ->paginate($this->perPage);
    }
}
