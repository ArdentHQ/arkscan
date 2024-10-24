<?php

declare(strict_types=1);

namespace App\Http\Livewire\Validators;

use App\Enums\SortDirection;
use App\Http\Livewire\Abstracts\TabbedTableComponent;
use App\Http\Livewire\Concerns\HasTableFilter;
use App\Http\Livewire\Concerns\HasTableSorting;
use App\Models\Transaction;
use App\Services\Timestamp;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @property bool $isAllSelected
 * @property LengthAwarePaginator $recentVotes
 * */
final class RecentVotes extends TabbedTableComponent
{
    use HasTableFilter;
    use HasTableSorting;

    public const INITIAL_SORT_KEY = 'age';

    public const INITIAL_SORT_DIRECTION = SortDirection::DESC;

    public array $filter = [
        'vote'      => true,
        'unvote'    => true,
        'vote-swap' => true,
    ];

    /** @var mixed */
    protected $listeners = [
        'setRecentVotesReady' => 'setIsReady',
    ];

    public function queryString(): array
    {
        return [
            'vote'      => ['except' => true],
            'unvote'    => ['except' => true],
            'vote-swap' => ['except' => true],
        ];
    }

    public function mount(bool $deferLoading = true): void
    {
        if (! $deferLoading) {
            $this->setIsReady();
        }
    }

    public function render(): View
    {
        return view('livewire.validators.recent-votes', [
            'votes' => ViewModelFactory::paginate($this->recentVotes),
        ]);
    }

    public function getNoResultsMessageProperty(): null|string
    {
        if (! $this->hasFilters()) {
            return trans('tables.recent-votes.no_results.no_filters');
        }

        if ($this->recentVotes->total() === 0) {
            return trans('tables.recent-votes.no_results.no_results');
        }

        return null;
    }

    public function getRecentVotesProperty(): LengthAwarePaginator
    {
        $emptyResults = new LengthAwarePaginator([], 0, $this->perPage);
        if (! $this->isReady) {
            return $emptyResults;
        }

        if (! $this->hasFilters()) {
            return $emptyResults;
        }

        return $this->getRecentVotesQuery()
            ->paginate($this->perPage);
    }

    private function hasFilters(): bool
    {
        if ($this->filter['vote'] === true) {
            return true;
        }

        if ($this->filter['unvote'] === true) {
            return true;
        }

        return $this->filter['vote-swap'] === true;
    }

    private function getRecentVotesQuery(): Builder
    {
        $sortDirection = SortDirection::ASC;
        if ($this->sortDirection === SortDirection::DESC) {
            $sortDirection = SortDirection::DESC;
        }

        // TODO: fixme
        return Transaction::query()
            ->where('type', 3)
            ->where('timestamp', '>=', Timestamp::now()->subDays(30)->unix() * 1000)
            ->where(function ($query) {
                $query->where(fn ($query) => $query->when($this->filter['vote'], function ($query) {
                    $query->whereRaw('jsonb_array_length(asset->\'votes\') >= 1')
                        ->whereRaw('jsonb_array_length(asset->\'unvotes\') = 0');
                }))->orWhere(fn ($query) => $query->when($this->filter['unvote'], function ($query) {
                    $query->whereRaw('jsonb_array_length(asset->\'unvotes\') >= 1')
                        ->whereRaw('jsonb_array_length(asset->\'votes\') = 0');
                }))->orWhere(fn ($query) => $query->when(
                    $this->filter['vote-swap'],
                    fn ($query) => $query
                        ->whereRaw('jsonb_array_length(asset->\'votes\') >= 1')
                        ->whereRaw('jsonb_array_length(asset->\'unvotes\') >= 1')
                ));
            })
            ->when($this->sortKey === 'age', fn ($query) => $query->sortByAge($sortDirection))
            ->when($this->sortKey === 'address', fn ($query) => $query->sortByAddress($sortDirection))
            ->when($this->sortKey === 'type', fn ($query) => $query->sortByType($sortDirection))
            ->when($this->sortKey === 'name', fn ($query) => $query->sortByUsername($sortDirection));
    }
}
