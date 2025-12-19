<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Inertia\Concerns\MissedBlocksTab;
use App\Http\Controllers\Inertia\Concerns\RecentVotesTab;
use App\Http\Controllers\Inertia\Concerns\ValidatorsTab;
use App\Http\Controllers\Inertia\Concerns\WithFilters;
use App\Http\Controllers\Inertia\Concerns\WithPagination;
use App\Http\Controllers\Inertia\Concerns\WithSorting;
use App\Models\ForgingStats;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\ValidatorCache;
use ARKEcosystem\Foundation\UserInterface\UI;
use Inertia\Inertia;
use Inertia\Response;

final class ValidatorsController
{
    use ValidatorsTab;
    use RecentVotesTab;
    use MissedBlocksTab;
    use WithPagination;
    use WithSorting;
    use WithFilters;

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
            'baseUrl'      => route('validators', absolute: false),

            'validators'   => Inertia::optional(function () {
                $paginator = $this->getValidators();

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->getValidatorsNoResultsMessageProperty($paginator->count()),
                    'perPageOptions'   => trans('tables.validators.validator_per_page_options'),
                ];
            }),

            'recentVotes' => Inertia::optional(function () {
                $paginator = $this->getRecentVotes();

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->getRecentVotesNoResultsMessageProperty($paginator->count()),
                ];
            }),

            'missedBlocks' => Inertia::optional(function () {
                $paginator = $this->getMissedBlocks();

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->getMissedBlocksNoResultsMessageProperty($paginator->count()),
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
}
