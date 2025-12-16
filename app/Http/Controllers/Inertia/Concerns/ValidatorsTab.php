<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia\Concerns;

use App\DTO\Inertia\IValidator;
use App\Enums\SortDirection;
use App\Facades\Network;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

trait ValidatorsTab
{
    public const VALIDATORS_PER_PAGE = 53;

    public const VALIDATORS_INITIAL_SORT_KEY = 'rank';

    public const VALIDATORS_INITIAL_SORT_DIRECTION = SortDirection::ASC;

    /** @var array<string, array<string, bool>> */
    protected array $validatorsFilters = [
        'validators' => [
            'active'   => true,
            'standby'  => true,
            'dormant'  => false,
            'resigned' => false,
        ],
    ];

    public function getValidatorsNoResultsMessageProperty(int $count): null|string
    {
        if (! $this->validatorsHasFilters()) {
            return (string) trans('tables.validators.no_results.no_filters');
        }

        return $count === 0
            ? (string) trans('tables.validators.no_results.no_results')
            : null;
    }

    public function getValidators(): LengthAwarePaginator
    {
        $emptyResults = new LengthAwarePaginator([], 0, $this->perPage('validators'), $this->page(), [
            'pageName' => 'page',
        ]);

        if (! $this->validatorsHasFilters()) {
            return $emptyResults;
        }

        return $this->getValidatorsQuery()
            ->paginate($this->perPage('validators'), page: $this->page())
            ->through(fn (Wallet $validator) => IValidator::fromModel($validator));
    }

    private function validatorsHasFilters(): bool
    {
        if ($this->hasFilter('active', $this->validatorsFilters['validators']['active'])) {
            return true;
        }

        if ($this->hasFilter('standby', $this->validatorsFilters['validators']['standby'])) {
            return true;
        }

        if ($this->hasFilter('dormant', $this->validatorsFilters['validators']['dormant'])) {
            return true;
        }

        return $this->hasFilter('resigned', $this->validatorsFilters['validators']['resigned']);
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
                $query->where(fn ($query) => $query->when($this->hasFilter('active', $this->validatorsFilters['validators']['active']), function ($query) {
                    $query->where(function ($query) {
                        $query->where('attributes->validatorResigned', null)
                            ->orWhere('attributes->validatorResigned', false);
                    })->where(function ($query) {
                        $query->whereNot('attributes->validatorPublicKey', null)
                            ->whereNot('attributes->validatorPublicKey', '');
                    })->whereRaw('COALESCE((attributes->>\'validatorRank\')::int, 0) <= ?', Network::validatorCount());
                }))
                ->orWhere(fn ($query) => $query->when($this->hasFilter('standby', $this->validatorsFilters['validators']['standby']), function ($query) {
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
                ->orWhere(fn ($query) => $query->when($this->hasFilter('dormant', $this->validatorsFilters['validators']['dormant']), function ($query) {
                    $query->where(function ($query) {
                        $query->where('attributes->validatorResigned', null)
                            ->orWhere('attributes->validatorResigned', false);
                    })->where(function ($query) {
                        $query->where('attributes->validatorPublicKey', null)
                            ->orWhere('attributes->validatorPublicKey', '');
                    });
                }))
                ->orWhere(fn ($query) => $query->when($this->hasFilter('resigned', $this->validatorsFilters['validators']['resigned']), fn ($query) => $query->where('attributes->validatorResigned', true)));
            }))
            ->when($this->sortKey('validators') === 'rank', fn ($query) => $query->sortByRank($sortDirection))
            ->when($this->sortKey('validators') === 'name', fn ($query) => $query->sortByUsername($sortDirection))
            ->when($this->sortKey('validators') === 'votes' || $this->sortKey('validators') === 'percentage_votes', fn ($query) => $query->sortByVoteCount($sortDirection))
            ->when($this->sortKey('validators') === 'no_of_voters', fn ($query) => $query->sortByNumberOfVoters($sortDirection))
            ->when($this->sortKey('validators') === 'missed_blocks', fn ($query) => $query->sortByMissedBlocks($sortDirection));
    }
}
