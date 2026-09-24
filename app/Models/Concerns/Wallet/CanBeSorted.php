<?php

declare(strict_types=1);

namespace App\Models\Concerns\Wallet;

use App\Enums\SortDirection;
use App\Facades\Network;
use App\Models\ForgingStats;
use App\Services\Cache\DelegateCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait CanBeSorted
{
    public function scopeSortByUsername(mixed $query, SortDirection $sortDirection): Builder
    {
        return $query->orderByRaw("(\"attributes\"->'delegate'->>'username')::text ".$sortDirection->value.', ("attributes"->\'delegate\'->>\'rank\')::numeric ASC');
    }

    public function scopeSortByRank(mixed $query, SortDirection $sortDirection): Builder
    {
        return $query->orderByRaw("(\"attributes\"->'delegate'->>'rank')::numeric ".$sortDirection->value);
    }

    public function scopeSortByVoteCount(mixed $query, SortDirection $sortDirection): Builder
    {
        return $query->selectRaw('("attributes"->\'delegate\'->>\'voteBalance\')::numeric AS vote_count')
            ->selectRaw('wallets.*')
            ->orderByRaw('CASE WHEN NULLIF(("attributes"->\'delegate\'->>\'voteBalance\')::numeric, 0) IS NULL THEN 1 ELSE 0 END ASC')
            ->orderByRaw(sprintf(
                '("attributes"->\'delegate\'->>\'voteBalance\')::numeric %s',
                $sortDirection->value
            ))
            ->orderByRaw('("attributes"->\'delegate\'->>\'rank\')::numeric ASC');
    }

    public function scopeSortByNumberOfVoters(mixed $query, SortDirection $sortDirection): Builder
    {
        $voterCounts = (new DelegateCache())->getAllVoterCounts();
        if (count($voterCounts) === 0) {
            return $query->selectRaw('0 AS no_of_voters')
                ->selectRaw('wallets.*');
        }

        $voterCounts = collect($voterCounts);

        $valuesList = implode(',', array_fill(0, count($voterCounts), '(?,?)'));

        $bindings = $voterCounts->flatMap(fn ($count, $publicKey) => [$publicKey, $count])->all();

        $query->join(DB::raw("(values {$valuesList}) as voting_stats (public_key, count)"), 'wallets.public_key', '=', 'voting_stats.public_key', 'left outer');
        $query->getQuery()->addBinding($bindings, 'join');

        return $query->selectRaw('voting_stats.count AS no_of_voters')
            ->selectRaw('wallets.*')
            ->orderByRaw(sprintf('no_of_voters %s NULLS LAST', $sortDirection->value))
            ->orderByRaw('("attributes"->\'delegate\'->>\'rank\')::numeric ASC');
    }

    public function scopeSortByMissedBlocks(mixed $query, SortDirection $sortDirection): Builder
    {
        $missedBlocks = ForgingStats::selectRaw('public_key, COUNT(*) as count')
            ->groupBy('public_key')
            ->whereNot('missed_height', null)
            ->get();

        if (count($missedBlocks) === 0) {
            return $query->selectRaw('0 AS missed_blocks')
                ->selectRaw('wallets.*');
        }

        $valuesList = implode(',', array_fill(0, count($missedBlocks), '(?,?)'));

        $bindings = $missedBlocks->flatMap(fn ($forgingStat) => [$forgingStat->public_key, $forgingStat->count])->all();

        $query->join(DB::raw("(values {$valuesList}) as forging_stats (public_key, count)"), 'wallets.public_key', '=', 'forging_stats.public_key', 'left outer');
        $query->getQuery()->addBinding($bindings, 'join');

        return $query->selectRaw('COALESCE(forging_stats.count, 0) AS missed_blocks')
            ->selectRaw('wallets.*')
            ->when($sortDirection === SortDirection::ASC, fn ($query) => $query->orderByRaw(sprintf(
                'CASE WHEN ("attributes"->\'delegate\'->>\'rank\')::numeric <= %d THEN 0 ELSE 1 END ASC',
                Network::delegateCount(),
            )))
            ->orderByRaw(sprintf(
                'missed_blocks %s',
                $sortDirection->value,
            ))
            ->orderByRaw('("attributes"->\'delegate\'->>\'rank\')::numeric ASC');
    }
}
