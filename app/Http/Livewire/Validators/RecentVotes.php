<?php

declare(strict_types=1);

namespace App\Http\Livewire\Validators;

use App\Enums\SortDirection;
use App\Http\Livewire\Abstracts\TabbedTableComponent;
use App\Http\Livewire\Concerns\HasTableFilter;
use App\Http\Livewire\Concerns\HasTableSorting;
use App\Models\Scopes\UnvoteScope;
use App\Models\Scopes\VoteScope;
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

    public const INITIAL_SORT_KEY = 'address';

    public const INITIAL_SORT_DIRECTION = SortDirection::DESC;

    public array $filter = [
        'vote'   => true,
        'unvote' => true,
    ];

    /** @var mixed */
    protected $listeners = [
        'setRecentVotesReady' => 'setIsReady',
    ];

    public function queryString(): array
    {
        return [
            'filter.vote'   => ['as' => 'vote', 'except' => true],
            'filter.unvote' => ['as' => 'unvote', 'except' => true],
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

        return $this->filter['unvote'] === true;
    }

    private function getRecentVotesQuery(): Builder
    {
        $sortDirection = SortDirection::ASC;
        if ($this->sortDirection === SortDirection::DESC) {
            $sortDirection = SortDirection::DESC;
        }

        return Transaction::query()
            ->join('receipts', 'transactions.hash', '=', 'receipts.transaction_hash')
            ->where('receipts.status', true)
            ->where('timestamp', '>=', Timestamp::now()->subDays(30)->unix() * 1000)
            ->where(function ($query) {
                $query->where(fn ($query) => $query->when($this->filter['vote'], function ($query) {
                    $query->withScope(VoteScope::class);
                }))
                ->orWhere(fn ($query) => $query->when($this->filter['unvote'], function ($query) {
                    $query->withScope(UnvoteScope::class);
                }));
            })
            ->when($this->sortKey === 'age', fn ($query) => $query->sortByAge($sortDirection))
            ->when($this->sortKey === 'address', fn ($query) => $query->sortByAddress($sortDirection))
            ->when($this->sortKey === 'type', fn ($query) => $query->sortByType($sortDirection))
            ->when($this->sortKey === 'name', fn ($query) => $query->sortByUsername($sortDirection));
    }
}
