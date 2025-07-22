<?php

declare(strict_types=1);

namespace App\Http\Livewire\Validators\Concerns;

use App\Enums\SortDirection;
use App\Models\ForgingStats;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;

/**
 * @property LengthAwarePaginator $missedBlocks
 * */
trait MissedBlocksTab
{
    public const MISSED_BLOCKS_INITIAL_SORT_KEY = 'age';

    public const MISSED_BLOCKS_INITIAL_SORT_DIRECTION = SortDirection::DESC;

    public $missedBlocksIsReady = false;

    public function mountMissedBlocksTab(bool $deferLoading = true): void
    {
        if (! $deferLoading) {
            $this->setMissedBlocksReady();
        }
    }

    public function queryStringMissedBlocksTab(): array
    {
        return [
            'paginators.missed-blocks'        => ['except' => 1, 'as' => 'page', 'history' => true],
            'paginatorsPerPage.missed-blocks' => ['except' => self::defaultPerPage('MISSED_BLOCKS'), 'as' => 'per-page', 'history' => true],
            'sortKeys.missed-blocks'          => ['as' => 'sort', 'except' => static::MISSED_BLOCKS_INITIAL_SORT_KEY],
            'sortDirections.missed-blocks'    => ['as' => 'sort-direction', 'except' => static::MISSED_BLOCKS_INITIAL_SORT_DIRECTION->value],
        ];
    }

    public function getMissedBlocksNoResultsMessageProperty(): null|string
    {
        if ($this->missedBlocks->total() === 0) {
            return trans('tables.missed-blocks.no_results');
        }

        return null;
    }

    public function getMissedBlocksProperty(): LengthAwarePaginator
    {
        if (! $this->isReady) {
            return new LengthAwarePaginator([], 0, $this->getPerPage('missed-blocks'), $this->getPage('missed-blocks'));
        }

        return $this->getMissedBlocksQuery()
            ->paginate($this->getPerPage('missed-blocks'), page: $this->getPage('missed-blocks'));
    }

    #[On('setMissedBlocksReady')]
    public function setMissedBlocksReady(): void
    {
        $this->missedBlocksIsReady = true;
    }

    private function getMissedBlocksQuery(): Builder
    {
        if (config('database.default') === 'sqlite') {
            return ForgingStats::with('validator')
                ->orderByDesc('timestamp')
                ->whereNotNull('missed_height');
        }

        $sortDirection = SortDirection::ASC;
        if ($this->getSortDirection('missed-blocks') === SortDirection::DESC) {
            $sortDirection = SortDirection::DESC;
        }

        return ForgingStats::query()
            ->with('validator')
            ->when($this->getSortKey('missed-blocks') === 'height', fn ($query) => $query->sortByHeight($sortDirection))
            ->when($this->getSortKey('missed-blocks') === 'age', fn ($query) => $query->sortByAge($sortDirection))
            ->when($this->getSortKey('missed-blocks') === 'name', fn ($query) => $query->sortByUsername($sortDirection))
            ->when($this->getSortKey('missed-blocks') === 'votes' || $this->getSortKey('missed-blocks') === 'percentage_votes', fn ($query) => $query->sortByVoteCount($sortDirection))
            ->when($this->getSortKey('missed-blocks') === 'no_of_voters', fn ($query) => $query->sortByNumberOfVoters($sortDirection))
            ->whereNotNull('missed_height');
    }
}
