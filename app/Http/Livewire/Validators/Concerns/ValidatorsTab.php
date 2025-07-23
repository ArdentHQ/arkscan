<?php

declare(strict_types=1);

namespace App\Http\Livewire\Validators\Concerns;

use App\Enums\SortDirection;
use App\Facades\Network;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;

/**
 * @property bool $isAllSelected
 * @property LengthAwarePaginator $validators
 * */
trait ValidatorsTab
{
    public const VALIDATORS_PER_PAGE = 53;

    public const VALIDATORS_INITIAL_SORT_KEY = 'rank';

    public const VALIDATORS_INITIAL_SORT_DIRECTION = SortDirection::ASC;

    public $validatorsIsReady = false;

    public function queryStringValidatorsTab(): array
    {
        return [
            'paginators.validators'        => ['except' => 1, 'as' => 'page', 'history' => true],
            'paginatorsPerPage.validators' => ['except' => self::defaultPerPage('VALIDATORS'), 'as' => 'per-page', 'history' => true],
            'sortKeys.validators'          => ['as' => 'sort', 'except' => self::defaultSortKey('VALIDATORS')],
            'sortDirections.validators'    => ['as' => 'sort-direction', 'except' => self::defaultSortDirection('VALIDATORS')->value],
            'filters.validators.active'    => ['as' => 'active', 'except' => true],
            'filters.validators.standby'   => ['as' => 'standby', 'except' => true],
            'filters.validators.resigned'  => ['as' => 'resigned', 'except' => false],
        ];
    }

    public function getValidatorsNoResultsMessageProperty(): null|string
    {
        if (! $this->validatorsHasFilters()) {
            return trans('tables.validators.no_results.no_filters');
        }

        if ($this->validators->total() === 0) {
            return trans('tables.validators.no_results.no_results');
        }

        return null;
    }

    public function getValidatorsProperty(): LengthAwarePaginator
    {
        $emptyResults = new LengthAwarePaginator([], 0, $this->getPerPage('validators'), $this->getPage('validators'));
        if (! $this->validatorsIsReady) {
            return $emptyResults;
        }

        if (! $this->validatorsHasFilters()) {
            return $emptyResults;
        }

        return $this->getValidatorsQuery()
            ->paginate($this->getPerPage('validators'), page: $this->getPage('validators'));
    }

    public static function validatorsPerPageOptions(): array
    {
        return trans('tables.validators.validator_per_page_options');
    }

    #[On('setValidatorsReady')]
    public function setValidatorsReady(): void
    {
        $this->validatorsIsReady = true;
    }

    private function validatorsHasFilters(): bool
    {
        if ($this->filters['validators']['active'] === true) {
            return true;
        }

        if ($this->filters['validators']['standby'] === true) {
            return true;
        }

        return $this->filters['validators']['resigned'] === true;
    }

    private function getValidatorsQuery(): Builder
    {
        $sortDirection = SortDirection::ASC;
        if ($this->getSortDirection('validators') === SortDirection::DESC) {
            $sortDirection = SortDirection::DESC;
        }

        return Wallet::query()
            ->whereNotNull('attributes->validatorPublicKey')
            ->where(fn ($query) => $query->when($this->validatorsHasFilters(), function ($query) {
                $query->where(fn ($query) => $query->when($this->filters['validators']['active'] === true, fn ($query) => $query->where(function ($query) {
                    $query->where('attributes->validatorResigned', null)
                        ->orWhere('attributes->validatorResigned', false);
                })->whereRaw('COALESCE((attributes->>\'validatorRank\')::int, 0) <= ?', Network::validatorCount())))
                    ->orWhere(fn ($query) => $query->when($this->filters['validators']['standby'] === true, fn ($query) => $query->where(function ($query) {
                        $query->where('attributes->validatorResigned', null)
                            ->orWhere('attributes->validatorResigned', false);
                    })->where(function ($query) {
                        $query->whereRaw('COALESCE((attributes->>\'validatorRank\')::int, 0) > ?', Network::validatorCount());
                    })))
                    ->orWhere(fn ($query) => $query->when($this->filters['validators']['resigned'] === true, fn ($query) => $query->where('attributes->validatorResigned', true)));
            }))
            ->when($this->getSortKey('validators') === 'rank', fn ($query) => $query->sortByRank($sortDirection))
            ->when($this->getSortKey('validators') === 'name', fn ($query) => $query->sortByUsername($sortDirection))
            ->when($this->getSortKey('validators') === 'votes' || $this->getSortKey('validators') === 'percentage_votes', fn ($query) => $query->sortByVoteCount($sortDirection))
            ->when($this->getSortKey('validators') === 'no_of_voters', fn ($query) => $query->sortByNumberOfVoters($sortDirection))
            ->when($this->getSortKey('validators') === 'missed_blocks', fn ($query) => $query->sortByMissedBlocks($sortDirection));
    }
}
