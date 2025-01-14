<?php

declare(strict_types=1);

namespace App\Http\Livewire\Delegates;

use App\Enums\SortDirection;
use App\Facades\Network;
use App\Http\Livewire\Abstracts\TabbedTableComponent;
use App\Http\Livewire\Concerns\HasTableFilter;
use App\Http\Livewire\Concerns\HasTableSorting;
use App\Models\Wallet;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @property bool $isAllSelected
 * @property LengthAwarePaginator $delegates
 * */
final class Delegates extends TabbedTableComponent
{
    use HasTableFilter;
    use HasTableSorting;

    public const PER_PAGE = 51;

    public const INITIAL_SORT_KEY = 'rank';

    public const INITIAL_SORT_DIRECTION = SortDirection::ASC;

    /**
     * Query string parameters.
     */
    public bool $active = true;

    public bool $standby = true;

    public bool $resigned = false;

    public array $filter = [
        'active'   => true,
        'standby'  => true,
        'resigned' => false,
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

    public static function perPageOptions(): array
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
        if ($this->sortDirection === SortDirection::DESC) {
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
            ->when($this->sortKey === 'rank', fn ($query) => $query->sortByRank($sortDirection))
            ->when($this->sortKey === 'name', fn ($query) => $query->sortByUsername($sortDirection))
            ->when($this->sortKey === 'votes' || $this->sortKey === 'percentage_votes', fn ($query) => $query->sortByVoteCount($sortDirection))
            ->when($this->sortKey === 'no_of_voters', fn ($query) => $query->sortByNumberOfVoters($sortDirection))
            ->when($this->sortKey === 'missed_blocks', fn ($query) => $query->sortByMissedBlocks($sortDirection));
    }
}
