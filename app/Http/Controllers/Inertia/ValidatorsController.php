<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\ForgingStats as ForgingStatsDTO;
use App\Enums\SortDirection;
use App\Http\Controllers\Inertia\Concerns\ValidatorsTab;
use App\Http\Controllers\Inertia\Concerns\WithPagination;
use App\Http\Livewire\Validators\Concerns\RecentVotesTab;
use App\Models\ForgingStats;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\ValidatorCache;
use ARKEcosystem\Foundation\UserInterface\UI;
use Illuminate\Pagination\AbstractPaginator;
use Inertia\Inertia;
use Inertia\Response;

final class ValidatorsController
{
    use ValidatorsTab;
    use RecentVotesTab;
    use WithPagination;

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
            'recentVotes' => Inertia::optional(function () {
                $paginator = $this->getRecentVotes();

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->getRecentVotesNoResultsMessageProperty($paginator->count()),
                ];
            }),
            'statistics' => [
                'voterCount'       => $voterCount,
                'totalVoted'       => $totalVoted,
                'votesPercentage'  => (new NetworkCache())->getVotesPercentage(),
                'missedBlocks'     => $missedBlockCount,
                'validatorsMissed' => $validatorsMissed,
            ],

            'missedBlocks' => Inertia::optional(function () {
                $paginator = $this->getMissedBlocks();

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->getMissedBlocksNoResultsMessageProperty($paginator->count()),
                ];
            }),
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

    // TODO: Re-implement sorting once the UI supports it - https://app.clickup.com/t/86dypp5jv
    //       Look at \App\Http\Livewire\Validators\Concerns\MissedBlocksTab for reference.
    //       Also check `getMissedBlocks` below
    private function sortDirection(string $name = 'default'): SortDirection
    {
        return SortDirection::ASC;
    }

    // TODO: Re-implement sorting once the UI supports it - https://app.clickup.com/t/86dypp5jv
    //       Look at \App\Http\Livewire\Validators\Concerns\MissedBlocksTab for reference.
    //       Also check `getMissedBlocks` below
    private function sortKey(string $name = 'default'): string
    {
        return 'rank';
    }

    private function getMissedBlocks(): AbstractPaginator
    {
        if (config('database.default') === 'sqlite') {
            return ForgingStats::with('validator')
                ->orderByDesc('timestamp')
                ->whereNotNull('missed_height')
                ->paginate($this->perPage(), page: $this->page())
                ->through(fn (ForgingStats $voter) => ForgingStatsDTO::fromModel($voter));
        }

        // TODO: Re-implement sorting once the UI supports it - https://app.clickup.com/t/86dypp5jv
        //       Look at \App\Http\Livewire\Validators\Concerns\MissedBlocksTab for reference.
        $missedBlocksSortKey = 'height';
        $sortDirection       = SortDirection::DESC;

        return ForgingStats::query()
            ->with('validator')
            // @phpstan-ignore-next-line
            ->when($missedBlocksSortKey === 'height', fn ($query) => $query->sortByHeight($sortDirection))
            // @phpstan-ignore-next-line
            ->when($missedBlocksSortKey === 'age', fn ($query) => $query->sortByAge($sortDirection))
            // @phpstan-ignore-next-line
            ->when($missedBlocksSortKey === 'name', fn ($query) => $query->sortByUsername($sortDirection))
            // @phpstan-ignore-next-line
            ->when($missedBlocksSortKey === 'votes' || $missedBlocksSortKey === 'percentage_votes', fn ($query) => $query->sortByVoteCount($sortDirection))
            // @phpstan-ignore-next-line
            ->when($missedBlocksSortKey === 'no_of_voters', fn ($query) => $query->sortByNumberOfVoters($sortDirection))
            ->whereNotNull('missed_height')
            ->paginate($this->perPage(), page: $this->page())
            ->through(fn (ForgingStats $voter) => ForgingStatsDTO::fromModel($voter));
    }

    private function getMissedBlocksNoResultsMessageProperty(int $count): null|string
    {
        if ($count === 0) {
            return trans('tables.missed-blocks.no_results');
        }

        return null;
    }
}
