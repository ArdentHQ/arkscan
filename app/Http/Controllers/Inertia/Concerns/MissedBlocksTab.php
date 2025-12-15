<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia\Concerns;

use App\DTO\Inertia\ForgingStats as ForgingStatsDTO;
use App\Enums\SortDirection;
use App\Models\ForgingStats;
use Illuminate\Pagination\AbstractPaginator;

trait MissedBlocksTab
{
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
