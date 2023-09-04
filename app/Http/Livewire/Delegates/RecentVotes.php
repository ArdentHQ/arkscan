<?php

declare(strict_types=1);

namespace App\Http\Livewire\Delegates;

use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\HasTableFilter;
use App\Http\Livewire\Concerns\HasTablePagination;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\Services\Timestamp;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;

/**
 * @property bool $isAllSelected
 * @property LengthAwarePaginator $recentVotes
 * */
final class RecentVotes extends Component
{
    use DeferLoading;
    use HasTableFilter;
    use HasTablePagination;

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
        return view('livewire.delegates.recent-votes', [
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
        return Transaction::query()
            ->withScope(OrderByTimestampScope::class)
            ->where('type', 3)
            ->where('timestamp', '>=', Timestamp::now()->subDays(30)->unix())
            ->where(function ($query) {
                $query->where(fn ($query) => $query->when($this->filter['vote'], function ($query) {
                    $query->whereRaw('jsonb_array_length(asset->\'votes\') = 1')
                        ->whereRaw('LEFT(asset->\'votes\'->>0, 1) = \'+\'');
                }))->orWhere(fn ($query) => $query->when($this->filter['unvote'], function ($query) {
                    $query->whereRaw('jsonb_array_length(asset->\'votes\') = 1')
                        ->whereRaw('LEFT(asset->\'votes\'->>0, 1) = \'-\'');
                }))->orWhere(fn ($query) => $query->when($this->filter['vote-swap'], fn ($query) => $query->whereRaw('jsonb_array_length(asset->\'votes\') = 2')));
            });
    }
}
