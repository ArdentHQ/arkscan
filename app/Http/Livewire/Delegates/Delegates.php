<?php

declare(strict_types=1);

namespace App\Http\Livewire\Delegates;

use App\Enums\SortDirection;
use App\Facades\Network;
use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\HasTableFilter;
use App\Http\Livewire\Concerns\HasTablePagination;
use App\Http\Livewire\Concerns\HasTableSorting;
use App\Models\ForgingStats;
use App\Models\Scopes\OrderByBalanceScope;
use App\Models\Wallet;
use App\Services\Cache\DelegateCache;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * @property bool $isAllSelected
 * @property LengthAwarePaginator $delegates
 * */
final class Delegates extends Component
{
    use DeferLoading;
    use HasTableFilter;
    use HasTablePagination;
    use HasTableSorting;

    public const PER_PAGE = 51;

    public const INITIAL_SORT_KEY = 'rank';

    public const INITIAL_SORT_DIRECTION = SortDirection::ASC;

    public array $filter = [
        'active'   => true,
        'standby'  => true,
        'resigned' => true,
    ];

    /** @var mixed */
    protected $listeners = [
        'setDelegatesReady' => 'setIsReady',
    ];

    public function queryString(): array
    {
        return [
            'active'   => ['except' => true],
            'standby'  => ['except' => true],
            'resigned' => ['except' => true],
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
        return view('livewire.delegates.delegates', [
            'delegates'  => ViewModelFactory::paginate($this->delegates),
        ]);
    }

    public function getNoResultsMessageProperty(): null|string
    {
        if (! $this->hasFilters()) {
            return trans('tables.delegates.no_results.no_filters');
        }

        if ($this->delegates->total() === 0) {
            return trans('tables.delegates.no_results.no_results');
        }

        return null;
    }

    public function getDelegatesProperty(): LengthAwarePaginator
    {
        $emptyResults = new LengthAwarePaginator([], 0, $this->perPage);
        if (! $this->isReady) {
            return $emptyResults;
        }

        if (! $this->hasFilters()) {
            return $emptyResults;
        }

        return $this->getDelegatesQuery()
            ->paginate($this->perPage);
    }

    public function perPageOptions(): array
    {
        return trans('tables.delegates.delegate_per_page_options');
    }

    private function hasFilters(): bool
    {
        if ($this->filter['active'] === true) {
            return true;
        }

        if ($this->filter['standby'] === true) {
            return true;
        }

        return $this->filter['resigned'] === true;
    }

    private function getDelegatesQuery(): Builder
    {
        $sortDirection = SortDirection::ASC;
        if ($this->sortDirection == SortDirection::DESC) {
            $sortDirection = SortDirection::DESC;
        }

        return Wallet::query()
            ->whereNotNull('attributes->delegate->username')
            ->where(fn ($query) => $query->when($this->hasFilters(), function ($query) {
                $query->where(fn ($query) => $query->when($this->filter['active'] === true, fn ($query) => $query->where(function ($query) {
                    $query->where('attributes->delegate->resigned', null)
                        ->orWhere('attributes->delegate->resigned', false);
                })->whereRaw('(attributes->\'delegate\'->>\'rank\')::int <= ?', Network::delegateCount())))
                    ->orWhere(fn ($query) => $query->when($this->filter['standby'] === true, fn ($query) => $query->where(function ($query) {
                        $query->where('attributes->delegate->resigned', null)
                            ->orWhere('attributes->delegate->resigned', false);
                    })->where(function ($query) {
                        $query->whereRaw('(attributes->\'delegate\'->>\'rank\')::int > ?', Network::delegateCount());
                    })))
                    ->orWhere(fn ($query) => $query->when($this->filter['resigned'] === true, fn ($query) => $query->where('attributes->delegate->resigned', true)));
            }))
            ->when($this->sortKey === 'rank', fn ($query) => $query->orderByRaw("(\"attributes\"->'delegate'->>'rank')::numeric ".$sortDirection->value))
            ->when($this->sortKey === 'name', fn ($query) => $query->orderByRaw("(\"attributes\"->'delegate'->>'username')::text ".$sortDirection->value))
            ->when($this->sortKey === 'votes', fn ($query) => $query->orderByRaw("(\"attributes\"->'delegate'->>'voteBalance')::numeric ".$sortDirection->value))
            ->when($this->sortKey === 'no_of_voters', function ($query) use ($sortDirection) {
                $query->selectRaw('coalesce(voting_stats.count, 0) AS no_of_voters')
                    ->selectRaw('wallets.*')
                    ->join(DB::raw(sprintf(
                        '(values %s) as voting_stats (public_key, count)',
                        collect((new DelegateCache)->getAllVoterCounts())
                            ->map(fn ($count, $publicKey) => sprintf('(\'%s\',%d)', $publicKey, $count))
                            ->join(','),
                    )), 'wallets.public_key', '=', 'voting_stats.public_key', 'left outer')
                    ->orderBy('no_of_voters', $sortDirection->value);
            })
            ->when($this->sortKey === 'missed_blocks', function ($query) use ($sortDirection) {
                $query->selectRaw('coalesce(forging_stats.count, 0) AS missed_blocks')
                    ->selectRaw('wallets.*')
                    ->join(DB::raw(sprintf(
                        '(values %s) as forging_stats (public_key, count)',
                        ForgingStats::selectRaw('public_key, COUNT(*) as count')
                            ->groupBy('public_key')
                            ->whereNot('missed_height', null)
                            ->get()
                            ->map(fn ($forgingStat) => sprintf('(\'%s\',%d)', $forgingStat->public_key, $forgingStat->count))
                            ->join(','),
                    )), 'wallets.public_key', '=', 'forging_stats.public_key', 'left outer')
                    ->orderBy('missed_blocks', $sortDirection->value);
            });
    }
}
