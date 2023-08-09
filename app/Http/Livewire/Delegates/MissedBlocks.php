<?php

declare(strict_types=1);

namespace App\Http\Livewire\Delegates;

use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\HasTablePagination;
use App\Models\ForgingStats;
use App\Models\Scopes\OrderByHeightScope;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;

/**
 * @property bool $isAllSelected
 * @property LengthAwarePaginator $delegates
 * */
final class MissedBlocks extends Component
{
    use DeferLoading;
    use HasTablePagination;

    /** @var mixed */
    protected $listeners = [
        'setMissedBlocksReady' => 'setIsReady',
    ];

    public function mount(bool $deferLoading = true): void
    {
        if (! $deferLoading) {
            $this->setIsReady();
        }
    }

    public function render(): View
    {
        return view('livewire.delegates.missed-blocks', [
            'blocks' => ViewModelFactory::paginate($this->missedBlocks),
        ]);
    }

    public function getNoResultsMessageProperty(): null|string
    {
        if ($this->missedBlocks->total() === 0) {
            return trans('tables.missed-blocks.no_results');
        }

        return null;
    }

    public function getMissedBlocksProperty(): LengthAwarePaginator
    {
        if (! $this->isReady) {
            return new LengthAwarePaginator([], 0, $this->perPage);
        }

        return $this->getMissedBlocksQuery()
            ->paginate($this->perPage);
    }

    private function getMissedBlocksQuery(): Builder
    {
        return ForgingStats::query()
            ->orderBy('missed_height', 'desc')
            ->whereNotNull('missed_height');
    }
}
