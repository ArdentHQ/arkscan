<?php

declare(strict_types=1);

namespace App\Http\Livewire\Validators;

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
use Illuminate\Support\Facades\Log;

/**
 * @property bool $isAllSelected
 * @property LengthAwarePaginator $validators
 * */
final class Validators extends TabbedTableComponent
{
    use HasTableFilter;
    use HasTableSorting;

    public const PER_PAGE = 53;

    public const INITIAL_SORT_KEY = 'rank';

    public const INITIAL_SORT_DIRECTION = SortDirection::ASC;

    public array $filter = [
        'active'   => true,
        'standby'  => true,
        'resigned' => false,
    ];

    /** @var mixed */
    protected $listeners = [
        'setValidatorsReady' => 'setIsReady',
    ];

    public function queryString(): array
    {
        return [
            'filter.active'   => ['as' => 'active', 'except' => true],
            'filter.standby'  => ['as' => 'standby', 'except' => true],
            'filter.resigned' => ['as' => 'resigned', 'except' => true],
        ];
    }

    public function boot(): void
    {
        Log::debug('Booting Validators');
    }

    public function mount(bool $deferLoading = true): void
    {
        Log::debug('Mounting Validators');
        if (! $deferLoading) {
            $this->setIsReady();
        }
    }

    public function render(): View
    {
        return view('livewire.validators.validators', [
            'validators' => ViewModelFactory::paginate($this->validators),
        ]);
    }

    public function getNoResultsMessageProperty(): null|string
    {
        if (! $this->hasFilters()) {
            return trans('tables.validators.no_results.no_filters');
        }

        if ($this->validators->total() === 0) {
            return trans('tables.validators.no_results.no_results');
        }

        return null;
    }

    public function getValidatorsProperty(): LengthAwarePaginator
    {
        $emptyResults = new LengthAwarePaginator([], 0, $this->internalPerPage);
        if (! $this->isReady) {
            return $emptyResults;
        }

        if (! $this->hasFilters()) {
            return $emptyResults;
        }

        return $this->getValidatorsQuery()
            ->paginate($this->internalPerPage);
    }

    public static function perPageOptions(): array
    {
        return trans('tables.validators.validator_per_page_options');
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

    private function getValidatorsQuery(): Builder
    {
        $sortDirection = SortDirection::ASC;
        if ($this->internalSortDirection === SortDirection::DESC) {
            $sortDirection = SortDirection::DESC;
        }

        $sortKey = $this->internalSortKey;

        return Wallet::query()
            ->whereNotNull('attributes->validatorPublicKey')
            ->where(fn ($query) => $query->when($this->hasFilters(), function ($query) {
                $query->where(fn ($query) => $query->when($this->filter['active'] === true, fn ($query) => $query->where(function ($query) {
                    $query->where('attributes->validatorResigned', null)
                        ->orWhere('attributes->validatorResigned', false);
                })->whereRaw('COALESCE((attributes->>\'validatorRank\')::int, 0) <= ?', Network::validatorCount())))
                    ->orWhere(fn ($query) => $query->when($this->filter['standby'] === true, fn ($query) => $query->where(function ($query) {
                        $query->where('attributes->validatorResigned', null)
                            ->orWhere('attributes->validatorResigned', false);
                    })->where(function ($query) {
                        $query->whereRaw('COALESCE((attributes->>\'validatorRank\')::int, 0) > ?', Network::validatorCount());
                    })))
                    ->orWhere(fn ($query) => $query->when($this->filter['resigned'] === true, fn ($query) => $query->where('attributes->validatorResigned', true)));
            }))
            ->when($sortKey === 'rank', fn ($query) => $query->sortByRank($sortDirection))
            ->when($sortKey === 'name', fn ($query) => $query->sortByUsername($sortDirection))
            ->when($sortKey === 'votes' || $sortKey === 'percentage_votes', fn ($query) => $query->sortByVoteCount($sortDirection))
            ->when($sortKey === 'no_of_voters', fn ($query) => $query->sortByNumberOfVoters($sortDirection))
            ->when($sortKey === 'missed_blocks', fn ($query) => $query->sortByMissedBlocks($sortDirection));
    }

    public function hydrate(): void
    {
        Log::debug('Hydrating Validators', [
            'page' => $this->getPage(),
            'isReady' => $this->isReady,
            'perPage' => $this->perPage,
            'sortKey' => $this->sortKey,
            'sortDirection' => $this->sortDirection,
        ]);
    }
}
