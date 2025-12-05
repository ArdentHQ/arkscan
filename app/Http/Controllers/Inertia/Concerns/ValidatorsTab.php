<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia\Concerns;

use App\DTO\Inertia\IValidator;
use App\DTO\Inertia\Wallet as WalletDTO;
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
    protected $validatorsFilters = [
        'validators' => [
            'active'   => true,
            'standby'  => true,
            'dormant'  => true,
            'resigned' => true,
        ],
    ];
    // public const VALIDATORS_PER_PAGE = 53;

    // public const VALIDATORS_INITIAL_SORT_KEY = 'rank';

    // public const VALIDATORS_INITIAL_SORT_DIRECTION = SortDirection::ASC;

    // public bool $validatorsIsReady = false;

    // public function queryStringValidatorsTab(): array
    // {
    //     return [
    //         'paginators.validators'        => ['except' => 1, 'as' => 'page', 'history' => true],
    //         'paginatorsPerPage.validators' => ['except' => self::defaultPerPage('VALIDATORS'), 'as' => 'per-page', 'history' => true],
    //         'sortKeys.validators'          => ['as' => 'sort', 'except' => self::defaultSortKey('VALIDATORS')],
    //         'sortDirections.validators'    => ['as' => 'sort-direction', 'except' => self::defaultSortDirection('VALIDATORS')->value],
    //         'filters.validators.active'    => ['as' => 'active', 'except' => true],
    //         'filters.validators.standby'   => ['as' => 'standby', 'except' => true],
    //         'filters.validators.dormant'   => ['as' => 'dormant', 'except' => false],
    //         'filters.validators.resigned'  => ['as' => 'resigned', 'except' => false],
    //     ];
    // }

    // // We're keeping it here as TabbedComponent has its own mount method
    // // and we can't override it with arguments.
    // public function mountValidatorsTab(bool $deferLoading = true): void
    // {
    //     if (! $deferLoading) {
    //         $this->setValidatorsReady();
    //     }
    // }

    // public function getValidatorsNoResultsMessageProperty(): null|string
    // {
    //     if (! $this->validatorsHasFilters()) {
    //         return trans('tables.validators.no_results.no_filters');
    //     }

    //     if ($this->validators->total() === 0) {
    //         return trans('tables.validators.no_results.no_results');
    //     }

    //     return null;
    // }

    public function getValidators(): LengthAwarePaginator
    {
        // $emptyResults = new LengthAwarePaginator([], 0, $this->perPage('validators'), $this->page('validators'));

        // if (! $this->validatorsIsReady) {
        //     return $emptyResults;
        // }

        // if (! $this->validatorsHasFilters()) {
        //     return $emptyResults;
        // }

        return $this->getValidatorsQuery()
            ->paginate($this->perPage('validators'), page: $this->page('validators'))
            ->through(fn (Wallet $validator) => IValidator::fromModel($validator));
    }

    // public static function validatorsPerPageOptions(): array
    // {
    //     return trans('tables.validators.validator_per_page_options');
    // }

    // #[On('setValidatorsReady')]
    // public function setValidatorsReady(): void
    // {
    //     $this->validatorsIsReady = true;
    // }

    private function validatorsHasFilters(): bool
    {
        return true;
        // if ($this->validatorsFilters['validators']['active'] === true) {
        //     return true;
        // }

        // if ($this->validatorsFilters['validators']['standby'] === true) {
        //     return true;
        // }

        // if ($this->validatorsFilters['validators']['dormant'] === true) {
        //     return true;
        // }

        // return $this->validatorsFilters['validators']['resigned'] === true;
    }

    private function getValidatorsQuery(): Builder
    {
        $sortDirection = SortDirection::ASC;
        if ($this->sortDirection('validators') === SortDirection::DESC) {
            $sortDirection = SortDirection::DESC;
        }

        return Wallet::query()
            ->whereNotNull('attributes->validatorPublicKey')
            ->where(fn ($query) => $query->when($this->validatorsHasFilters(), function ($query) {
                $query->where(fn ($query) => $query->when($this->validatorsFilters['validators']['active'] === true, function ($query) {
                    $query->where(function ($query) {
                        $query->where('attributes->validatorResigned', null)
                            ->orWhere('attributes->validatorResigned', false);
                    })->where(function ($query) {
                        $query->whereNot('attributes->validatorPublicKey', null)
                            ->whereNot('attributes->validatorPublicKey', '');
                    })->whereRaw('COALESCE((attributes->>\'validatorRank\')::int, 0) <= ?', Network::validatorCount());
                }))
                ->orWhere(fn ($query) => $query->when($this->validatorsFilters['validators']['standby'] === true, function ($query) {
                    $query->where(function ($query) {
                        $query->where('attributes->validatorResigned', null)
                            ->orWhere('attributes->validatorResigned', false);
                    })->where(function ($query) {
                        $query->whereNot('attributes->validatorPublicKey', null)
                            ->whereNot('attributes->validatorPublicKey', '');
                    })->where(function ($query) {
                        $query->whereRaw('COALESCE((attributes->>\'validatorRank\')::int, 0) > ?', Network::validatorCount());
                    });
                }))
                ->orWhere(fn ($query) => $query->when($this->validatorsFilters['validators']['dormant'] === true, function ($query) {
                    $query->where(function ($query) {
                        $query->where('attributes->validatorResigned', null)
                            ->orWhere('attributes->validatorResigned', false);
                    })->where(function ($query) {
                        $query->where('attributes->validatorPublicKey', null)
                            ->orWhere('attributes->validatorPublicKey', '');
                    });
                }))
                ->orWhere(fn ($query) => $query->when($this->validatorsFilters['validators']['resigned'] === true, fn ($query) => $query->where('attributes->validatorResigned', true)));
            }))
            ->when($this->sortKey('validators') === 'rank', fn ($query) => $query->sortByRank($sortDirection))
            ->when($this->sortKey('validators') === 'name', fn ($query) => $query->sortByUsername($sortDirection))
            ->when($this->sortKey('validators') === 'votes' || $this->sortKey('validators') === 'percentage_votes', fn ($query) => $query->sortByVoteCount($sortDirection))
            ->when($this->sortKey('validators') === 'no_of_voters', fn ($query) => $query->sortByNumberOfVoters($sortDirection))
            ->when($this->sortKey('validators') === 'missed_blocks', fn ($query) => $query->sortByMissedBlocks($sortDirection));
    }
}
