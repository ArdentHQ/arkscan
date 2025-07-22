<?php

declare(strict_types=1);

namespace App\Http\Livewire\Validators\Concerns;

use App\Enums\SortDirection;
use App\Models\Scopes\UnvoteScope;
use App\Models\Scopes\VoteScope;
use App\Models\Transaction;
use App\Services\Timestamp;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;

/**
 * @property bool $isAllSelected
 * @property LengthAwarePaginator $recentVotes
 * */
trait RecentVotesTab
{
    public const RECENT_VOTES_INITIAL_SORT_KEY = 'age';

    public const RECENT_VOTES_INITIAL_SORT_DIRECTION = SortDirection::DESC;

    public $recentVotesIsReady = false;

    public function queryStringRecentVotesTab(): array
    {
        return [
            'paginators.recent-votes'        => ['except' => 1, 'as' => 'page', 'history' => true],
            'paginatorsPerPage.recent-votes' => ['except' => self::defaultPerPage('RECENT_VOTES'), 'as' => 'per-page', 'history' => true],
            'sortKeys.recent-votes'          => ['as' => 'sort', 'except' => self::defaultSortKey('RECENT_VOTES')],
            'sortDirections.recent-votes'    => ['as' => 'sort-direction', 'except' => self::defaultSortDirection('RECENT_VOTES')->value],
            'filters.recent-votes.vote'      => ['as' => 'vote', 'except' => true],
            'filters.recent-votes.unvote'    => ['as' => 'unvote', 'except' => true],
        ];
    }

    public function mount(bool $deferLoading = true): void
    {
        if (! $deferLoading) {
            $this->setRecentVotesIsReady();
        }
    }

    public function getRecentVotesNoResultsMessageProperty(): null|string
    {
        if (! $this->recentVotesHasFilters()) {
            return trans('tables.recent-votes.no_results.no_filters');
        }

        if ($this->recentVotes->total() === 0) {
            return trans('tables.recent-votes.no_results.no_results');
        }

        return null;
    }

    public function getRecentVotesProperty(): LengthAwarePaginator
    {
        $emptyResults = new LengthAwarePaginator([], 0, $this->getPerPage('recent-votes'), $this->getPage('recent-votes'));
        if (! $this->isReady) {
            return $emptyResults;
        }

        if (! $this->recentVotesHasFilters()) {
            return $emptyResults;
        }

        return $this->getRecentVotesQuery()
            ->paginate($this->getPerPage('recent-votes'), page: $this->getPage('recent-votes'));
    }

    #[On('setRecentVotesReady')]
    public function setRecentVotesReady(): void
    {
        $this->recentVotesIsReady = true;
    }

    private function recentVotesHasFilters(): bool
    {
        if ($this->filters['recent-votes']['vote'] === true) {
            return true;
        }

        return $this->filters['recent-votes']['unvote'] === true;
    }

    private function getRecentVotesQuery(): Builder
    {
        $sortDirection = SortDirection::ASC;
        if ($this->getSortDirection('recent-votes') === SortDirection::DESC) {
            $sortDirection = SortDirection::DESC;
        }

        return Transaction::query()
            ->with('votedFor')
            ->join('receipts', 'transactions.hash', '=', 'receipts.transaction_hash')
            ->where('receipts.status', true)
            ->where('timestamp', '>=', Timestamp::now()->subDays(30)->unix() * 1000)
            ->where(function ($query) {
                $query->where(fn ($query) => $query->when($this->filters['recent-votes']['vote'], function ($query) {
                    $query->withScope(VoteScope::class);
                }))
                ->orWhere(fn ($query) => $query->when($this->filters['recent-votes']['unvote'], function ($query) {
                    $query->withScope(UnvoteScope::class);
                }));
            })
            ->when($this->getSortKey('recent-votes') === 'age', fn ($query) => $query->sortByAge($sortDirection))
            ->when($this->getSortKey('recent-votes') === 'address', fn ($query) => $query->sortByAddress($sortDirection))
            ->when($this->getSortKey('recent-votes') === 'type', fn ($query) => $query->sortByType($sortDirection))
            ->when($this->getSortKey('recent-votes') === 'name', fn ($query) => $query->sortByUsername($sortDirection));
    }
}
