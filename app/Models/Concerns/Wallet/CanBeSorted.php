<?php

declare(strict_types=1);

namespace App\Models\Concerns\Wallet;

use App\Enums\SortDirection;
use App\Facades\Network;
use App\Models\ForgingStats;
use App\Services\Cache\DelegateCache;
use Illuminate\Support\Facades\DB;

trait CanBeSorted
{
    public function scopeSortByUsername(mixed $query, SortDirection $sortDirection): mixed
    {
        return $query->orderByRaw("(\"attributes\"->'delegate'->>'username')::text ".$sortDirection->value.', ("attributes"->\'delegate\'->>\'rank\')::numeric ASC');
    }

    public function scopeSortByRank(mixed $query, SortDirection $sortDirection): mixed
    {
        return $query->orderByRaw("(\"attributes\"->'delegate'->>'rank')::numeric ".$sortDirection->value);
    }

    public function scopeSortByVoteCount(mixed $query, SortDirection $sortDirection): mixed
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

    public function scopeSortByNumberOfVoters(mixed $query, SortDirection $sortDirection): mixed
    {
        $voterCounts = (new DelegateCache())->getAllVoterCounts();
        if (count($voterCounts) === 0) {
            return $query->selectRaw('0 AS no_of_voters')
                ->selectRaw('wallets.*');
        }

        return $query->selectRaw('voting_stats.count AS no_of_voters')
            ->selectRaw('wallets.*')
            ->join(DB::raw(sprintf(
                '(values %s) as voting_stats (public_key, count)',
                collect($voterCounts)
                    ->map(fn ($count, $publicKey) => sprintf('(\'%s\',%d)', $publicKey, $count))
                    ->join(','),
            )), 'wallets.public_key', '=', 'voting_stats.public_key', 'left outer')
            ->orderByRaw(sprintf('no_of_voters %s NULLS LAST', $sortDirection->value))
            ->orderByRaw('("attributes"->\'delegate\'->>\'rank\')::numeric ASC');
    }

    public function scopeSortByMissedBlocks(mixed $query, SortDirection $sortDirection): mixed
    {
        $missedBlocks = ForgingStats::selectRaw('public_key, COUNT(*) as count')
            ->groupBy('public_key')
            ->whereNot('missed_height', null)
            ->get();

        if (count($missedBlocks) === 0) {
            return $query->selectRaw('0 AS missed_blocks')
                ->selectRaw('wallets.*');
        }

        return $query->selectRaw('COALESCE(forging_stats.count, 0) AS missed_blocks')
            ->selectRaw('wallets.*')
            ->join(DB::raw(sprintf(
                '(values %s) as forging_stats (public_key, count)',
                $missedBlocks->map(fn ($forgingStat) => sprintf('(\'%s\',%d)', $forgingStat->public_key, $forgingStat->count))
                    ->join(','),
            )), 'wallets.public_key', '=', 'forging_stats.public_key', 'left outer')
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
