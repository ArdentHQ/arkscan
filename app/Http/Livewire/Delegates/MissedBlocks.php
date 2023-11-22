<?php

declare(strict_types=1);

namespace App\Http\Livewire\Delegates;

use App\Enums\SortDirection;
use App\Http\Livewire\Abstracts\TabbedTableComponent;
use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\HasTableSorting;
use App\Models\ForgingStats;
use App\Models\Wallet;
use App\Services\Cache\DelegateCache;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * @property LengthAwarePaginator $missedBlocks
 * */
final class MissedBlocks extends TabbedTableComponent
{
    use DeferLoading;
    use HasTableSorting;

    public const INITIAL_SORT_KEY = 'age';

    public const INITIAL_SORT_DIRECTION = SortDirection::DESC;

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
        if (config('database.default') === 'sqlite') {
            return ForgingStats::orderByDesc('timestamp')
                ->whereNotNull('missed_height');
        }

        $sortDirection = SortDirection::ASC;
        if ($this->sortDirection === SortDirection::DESC) {
            $sortDirection = SortDirection::DESC;
        }

        return ForgingStats::query()
            ->when($this->sortKey === 'height', fn ($query) => $query->sortByHeight($sortDirection))
            ->when($this->sortKey === 'age', fn ($query) => $query->sortByAge($sortDirection))
            ->when($this->sortKey === 'name', fn ($query) => $query->sortByUsername($sortDirection))
            ->when($this->sortKey === 'votes' || $this->sortKey === 'percentage_votes', fn ($query) => $query->sortByVoteCount($sortDirection))
            ->when($this->sortKey === 'no_of_voters', fn ($query) => $query->sortByNumberOfVoters($sortDirection))
            ->whereNotNull('missed_height');
    }
}
