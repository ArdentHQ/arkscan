<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia\Concerns;

use App\DTO\Inertia\ForgingStats as ForgingStatsDTO;
use App\Enums\SortDirection;
use App\Models\ForgingStats;
use Illuminate\Pagination\AbstractPaginator;

trait MissedBlocksTab
{
    public const MISSED_BLOCKS_INITIAL_SORT_KEY = 'age';

    public const MISSED_BLOCKS_INITIAL_SORT_DIRECTION = SortDirection::DESC;

    private function getMissedBlocks(): AbstractPaginator
    {
        if (config('database.default') === 'sqlite') {
            return ForgingStats::with('validator')
                ->orderByDesc('timestamp')
                ->whereNotNull('missed_height')
                ->paginate($this->perPage(), page: $this->page())
                ->through(fn (ForgingStats $voter) => ForgingStatsDTO::fromModel($voter));
        }

        $sortDirection = SortDirection::ASC;
        if ($this->sortDirection('missed-blocks') === SortDirection::DESC) {
            $sortDirection = SortDirection::DESC;
        }

        $sortBy = $this->sortKey('missed-blocks');

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
