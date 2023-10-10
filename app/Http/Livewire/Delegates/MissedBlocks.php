<?php

declare(strict_types=1);

namespace App\Http\Livewire\Delegates;

use App\Enums\SortDirection;
use App\Http\Livewire\Abstracts\TabbedTableComponent;
use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\HasTableSorting;
use App\Models\ForgingStats;
use App\Models\Wallet;
use App\Services\Cache\DelegateCache;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * @property LengthAwarePaginator $missedBlocks
 * */
final class MissedBlocks extends TabbedTableComponent
{
    use DeferLoading;
    use HasTableSorting;

    public const INITIAL_SORT_KEY = 'age';

    public const INITIAL_SORT_DIRECTION = SortDirection::DESC;

    /** @var mixed */
    protected $listeners = [
        'setMissedBlocksReady' => 'setIsReady',
    ];

    public function mount(bool $deferLoading = true): void
    {
        if (! $deferLoading) {
            $this->setIsReady();
        }
    }

    public function render(): View
    {
        return view('livewire.delegates.missed-blocks', [
            'blocks' => ViewModelFactory::paginate($this->missedBlocks),
        ]);
    }

    public function getNoResultsMessageProperty(): null|string
    {
        if ($this->missedBlocks->total() === 0) {
            return trans('tables.missed-blocks.no_results');
        }

        return null;
    }

    public function getMissedBlocksProperty(): LengthAwarePaginator
    {
        if (! $this->isReady) {
            return new LengthAwarePaginator([], 0, $this->perPage);
        }

        return $this->getMissedBlocksQuery()
            ->paginate($this->perPage);
    }

    private function getMissedBlocksQuery(): Builder
    {
        $sortDirection = SortDirection::ASC;
        if ($this->sortDirection === SortDirection::DESC) {
            $sortDirection = SortDirection::DESC;
        }

        return ForgingStats::query()
            ->when($this->sortKey === 'height', fn ($query) => $query->orderByRaw('missed_height '.$sortDirection->value.', timestamp DESC'))
            ->when($this->sortKey === 'age', fn ($query) => $query->orderByRaw('timestamp '.$sortDirection->value.', timestamp DESC'))
            ->when($this->sortKey === 'name', function ($query) use ($sortDirection) {
                $missedBlockPublicKeys = ForgingStats::groupBy('public_key')->pluck('public_key');

                $delegateNames = Wallet::whereIn('public_key', $missedBlockPublicKeys)
                    ->get()
                    ->pluck('attributes.delegate.username', 'public_key');

                if (count($delegateNames) === 0) {
                    $query->selectRaw('NULL AS delegate_name')
                        ->selectRaw('forging_stats.*');

                    return;
                }

                if (config('database.default') === 'sqlite') {
                    Schema::create('temp_delegate_names', function (Blueprint $table) {
                        $table->temporary();
                        $table->string('public_key');
                        $table->string('name');
                    });

                    DB::table('temp_delegate_names')
                        ->insert(
                            $delegateNames->map(fn ($name, $publicKey) => [
                                'public_key' => $publicKey,
                                'name' => $name,
                            ])
                            ->toArray()
                        );

                    $query->selectRaw('wallets.name AS delegate_name')
                        ->selectRaw('forging_stats.*')
                        ->join('temp_delegate_names AS wallets', 'forging_stats.public_key', '=', 'wallets.public_key', 'left outer')
                        ->orderByRaw('delegate_name '.$sortDirection->value.', timestamp DESC');
                } else {
                    $query->selectRaw('wallets.name AS delegate_name')
                        ->selectRaw('forging_stats.*')
                        ->join(DB::raw(sprintf(
                            '(values %s) as wallets (public_key, name)',
                            $delegateNames->map(fn ($name, $publicKey) => sprintf('(\'%s\',\'%s\')', $publicKey, $name))
                                ->join(','),
                        )), 'forging_stats.public_key', '=', 'wallets.public_key', 'left outer')
                        ->orderByRaw('delegate_name '.$sortDirection->value.', timestamp DESC');
                }
            })
            ->when($this->sortKey === 'votes' || $this->sortKey === 'percentage_votes', function ($query) use ($sortDirection) {
                $missedBlockPublicKeys = ForgingStats::groupBy('public_key')->pluck('public_key');

                $delegateVotes = Wallet::whereIn('public_key', $missedBlockPublicKeys)
                    ->get()
                    ->pluck('attributes.delegate.voteBalance', 'public_key');

                if (count($delegateVotes) === 0) {
                    $query->selectRaw('0 AS votes')
                        ->selectRaw('forging_stats.*');

                    return;
                }

                if (config('database.default') === 'sqlite') {
                    Schema::create('temp_delegate_votes', function (Blueprint $table) {
                        $table->temporary();
                        $table->string('public_key');
                        $table->integer('votes');
                    });

                    DB::table('temp_delegate_votes')
                        ->insert(
                            $delegateVotes->map(fn ($votes, $publicKey) => [
                                'public_key' => $publicKey,
                                'votes' => $votes,
                            ])
                            ->toArray()
                        );

                    $query->selectRaw('wallets.votes AS votes')
                        ->selectRaw('forging_stats.*')
                        ->join('temp_delegate_votes AS wallets', 'forging_stats.public_key', '=', 'wallets.public_key', 'left outer')
                        ->orderByRaw('votes '.$sortDirection->value.', timestamp DESC');
                } else {
                    $query->selectRaw('wallets.votes AS votes')
                        ->selectRaw('forging_stats.*')
                        ->join(DB::raw(sprintf(
                            '(values %s) as wallets (public_key, votes)',
                            $delegateVotes->map(fn ($votes, $publicKey) => sprintf('(\'%s\',%d)', $publicKey, $votes))
                                ->join(','),
                        )), 'forging_stats.public_key', '=', 'wallets.public_key', 'left outer')
                        ->orderByRaw('votes '.$sortDirection->value.', timestamp DESC');
                }
            })
            ->when($this->sortKey === 'no_of_voters', function ($query) use ($sortDirection) {
                $voterCounts = (new DelegateCache())->getAllVoterCounts();
                if (count($voterCounts) === 0) {
                    $query->selectRaw('0 AS no_of_voters')
                        ->selectRaw('forging_stats.*');

                    return;
                }

                if (config('database.default') === 'sqlite') {
                    Schema::create('temp_voter_counts', function (Blueprint $table) {
                        $table->temporary();
                        $table->string('public_key');
                        $table->integer('count');
                    });

                    DB::table('temp_voter_counts')
                        ->insert(
                            collect($voterCounts)->map(fn ($count, $publicKey) => [
                                'public_key' => $publicKey,
                                'count' => $count,
                            ])
                            ->toArray()
                        );

                    $query->selectRaw('voting_stats.count AS no_of_voters')
                        ->selectRaw('forging_stats.*')
                        ->join('temp_voter_counts AS voting_stats', 'forging_stats.public_key', '=', 'voting_stats.public_key', 'left outer')
                        ->orderByRaw(sprintf('no_of_voters %s NULLS LAST, timestamp DESC', $sortDirection->value));
                } else {
                    $query->selectRaw('voting_stats.count AS no_of_voters')
                        ->selectRaw('forging_stats.*')
                        ->join(DB::raw(sprintf(
                            '(values %s) as voting_stats (public_key, count)',
                            collect($voterCounts)
                                ->map(fn ($count, $publicKey) => sprintf('(\'%s\',%d)', $publicKey, $count))
                                ->join(','),
                        )), 'forging_stats.public_key', '=', 'voting_stats.public_key', 'left outer')
                        ->orderByRaw(sprintf('no_of_voters %s NULLS LAST, timestamp DESC', $sortDirection->value));
                }
            })
            ->whereNotNull('missed_height');
    }
}
