<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\ForgingStats as ForgingStatsDTO;
use App\Enums\SortDirection;
use App\Http\Controllers\Inertia\Concerns\WithPagination;
use App\Models\ForgingStats;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\ValidatorCache;
use ARKEcosystem\Foundation\UserInterface\UI;
use Illuminate\Pagination\AbstractPaginator;
use Inertia\Inertia;
use Inertia\Response;

final class ValidatorsController
{
    use WithPagination;

    public function __invoke(): Response
    {
        [$missedBlockCount, $validatorsMissed] = $this->missedBlocks();

        $validatorCache = new ValidatorCache();
        $voterCount     = $validatorCache->getTotalWalletsVoted();
        $totalVoted     = $validatorCache->getTotalBalanceVoted();

        return Inertia::render('Validators/Validators', [
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

    private function getMissedBlocks(): AbstractPaginator
    {
        if (config('database.default') === 'sqlite') {
            return ForgingStats::with('validator')
                ->orderByDesc('timestamp')
                ->whereNotNull('missed_height')
                ->paginate($this->perPage(), page: $this->page())
                ->through(fn (ForgingStats $voter) => ForgingStatsDTO::fromModel($voter));
        }

        $sortBy        = request()->query('sort', 'age');
        $sortDirection = SortDirection::ASC;

        if (request()->query('sort-direction') === SortDirection::ASC->value) {
            $sortDirection = SortDirection::ASC;
        } else if ($sortBy === 'age') {
            // Default sort direction for age is DESC
            $sortDirection = SortDirection::DESC;
        }

        return ForgingStats::query()
            ->with('validator')
            ->when($sortBy === 'height', fn ($query) => $query->sortByHeight($sortDirection))
            ->when($sortBy === 'age', fn ($query) => $query->sortByAge($sortDirection))
            ->when($sortBy === 'name', fn ($query) => $query->sortByUsername($sortDirection))
            ->when($sortBy === 'votes' || $sortBy === 'percentage_votes', fn ($query) => $query->sortByVoteCount($sortDirection))
            ->when($sortBy === 'no_of_voters', fn ($query) => $query->sortByNumberOfVoters($sortDirection))
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
