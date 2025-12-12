<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia\Concerns;

use App\DTO\Inertia\Transaction as ITransaction;
use App\Enums\SortDirection;
use App\Models\Scopes\UnvoteScope;
use App\Models\Scopes\VoteScope;
use App\Models\Transaction;
use App\Services\Timestamp;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

trait RecentVotesTab
{
    public const RECENT_VOTES_INITIAL_SORT_KEY = 'age';

    public const RECENT_VOTES_INITIAL_SORT_DIRECTION = SortDirection::DESC;

    /** @var array<string, array<string, bool>> */
    protected array $recentVotesFilters = [
       'recent-votes' => [
           'vote'   => true,
           'unvote' => true,
       ],
    ];

    public function getRecentVotesNoResultsMessageProperty(int $count): null|string
    {
        if (! $this->recentVotesHasFilters()) {
            return (string) trans('tables.recent-votes.no_results.no_filters');
        }

        return $count === 0
            ? (string) trans('tables.recent-votes.no_results.no_results')
            : null;
        
    }

    public function getRecentVotes(): LengthAwarePaginator
    {
        $emptyResults = new LengthAwarePaginator([], 0, $this->perPage('recent-votes'), $this->page(), [
            'pageName' => 'page',
        ]);

        if (! $this->recentVotesHasFilters()) {
            return $emptyResults;
        }

        return $this->getRecentVotesQuery()
            ->paginate($this->perPage('recent-votes'), page: $this->page(), pageName: 'page')
            ->through(fn (Transaction $transaction) => ITransaction::fromModel($transaction));
    }

    private function recentVotesHasFilters(): bool
    {
        if ($this->hasFilter('vote', $this->recentVotesFilters['recent-votes']['vote'])) {
            return true;
        }

        return $this->hasFilter('unvote', $this->recentVotesFilters['recent-votes']['unvote']);
    }

    private function getRecentVotesQuery(): Builder
    {
        $sortDirection = SortDirection::ASC;
        if ($this->sortDirection('recent-votes') === SortDirection::DESC) {
            $sortDirection = SortDirection::DESC;
        }

        return Transaction::query()
            ->with('votedFor')
            ->where('status', true)
            ->where('timestamp', '>=', Timestamp::now()->subDays(30)->unix() * 1000)
            ->where(function ($query) {
                $query->where(fn ($query) => $query->when($this->hasFilter('vote', $this->recentVotesFilters['recent-votes']['vote']), function ($query) {
                    $query->withScope(VoteScope::class);
                }))
                ->orWhere(fn ($query) => $query->when($this->hasFilter('unvote', $this->recentVotesFilters['recent-votes']['unvote']), function ($query) {
                    $query->withScope(UnvoteScope::class);
                }));
            })
            ->when($this->sortKey('recent-votes') === 'age', fn ($query) => $query->sortByAge($sortDirection))
            ->when($this->sortKey('recent-votes') === 'address', fn ($query) => $query->sortByAddress($sortDirection))
            ->when($this->sortKey('recent-votes') === 'type', fn ($query) => $query->sortByType($sortDirection))
            ->when($this->sortKey('recent-votes') === 'name', fn ($query) => $query->sortByUsername($sortDirection));
    }
}
