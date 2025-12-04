<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Inertia\Concerns\ValidatorsTab;
use App\Models\ForgingStats;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\ValidatorCache;
use ARKEcosystem\Foundation\UserInterface\UI;
use Inertia\Inertia;
use Inertia\Response;

final class ValidatorsController
{
    use ValidatorsTab;

    public const FILTERS = [
        'validators' => [
            'active'   => true,
            'standby'  => true,
            'dormant'  => false,
            'resigned' => false,
        ],
        'recent-votes' => [
            'vote'   => true,
            'unvote' => true,
        ],
    ];

    public function __invoke(): Response
    {
        [$missedBlockCount, $validatorsMissed] = $this->missedBlocks();

        $validatorCache = new ValidatorCache();
        $voterCount     = $validatorCache->getTotalWalletsVoted();
        $totalVoted     = $validatorCache->getTotalBalanceVoted();

        return Inertia::render('Validators/Validators', [
            'filters'      => self::FILTERS,
            'validators'   => Inertia::optional(function () {
                $paginator = $this->getValidators();

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->getValidatorsNoResultsMessageProperty($paginator->count()),
                ];
            }),
            'statistics' => [
                'voterCount'       => $voterCount,
                'totalVoted'       => $totalVoted,
                'votesPercentage'  => (new NetworkCache())->getVotesPercentage(),
                'missedBlocks'     => $missedBlockCount,
                'validatorsMissed' => $validatorsMissed,
            ],
        ]);
    }

    private function missedBlocks(): array
    {
        $stats = ForgingStats::where('forged', false)->get();

        return [
            $stats->count(),
            $stats->unique('address')->count(),
        ];
    }

    private function getValidatorsNoResultsMessageProperty(int $count): null|string
    {
        // if (! $this->hasAddressingFilters() && ! $this->hasTransactionTypeFilters()) {
        //     return trans('tables.transactions.no_results.no_filters');
        // }

        // if (! $this->hasAddressingFilters()) {
        //     return trans('tables.transactions.no_results.no_addressing_filters');
        // }

        // if ($count === 0) {
        //     return trans('tables.transactions.no_results.no_results');
        // }

        return null;
    }

    private function page(string $name = 'default'): int
    {
        if (request()->has('page')) {
            return (int) request()->get('page');
        }

        // @TOOD: handle page name

        return 1;
    }

    private function perPage(string $name = 'default'): int
    {
        if (request()->has('per-page')) {
            return (int) request()->get('per-page');
        }

        if (defined(static::class.'::'.$name.'PER_PAGE')) {
            return dd(constant(static::class.'::'.$name.'PER_PAGE'));
        }

        return (int) config('arkscan.pagination.per_page');
    }

    private function sortDirection(string $name = 'default'): string
    {
        return 'asc';
    }

    private function sortKey(string $name = 'default'): string
    {
        return 'rank';
    }
}
