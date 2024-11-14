<?php

declare(strict_types=1);

namespace App\Models\Concerns\Wallet;

use App\Enums\SortDirection;
use App\Facades\Network;
use App\Models\ForgingStats;
use App\Services\Cache\ValidatorCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait CanBeSorted
{
    public function scopeSortByRank(mixed $query, SortDirection $sortDirection): Builder
    {
        return $query->orderByRaw("(\"attributes\"->>'validatorRank')::numeric ".$sortDirection->value);
    }

    public function scopeSortByVoteCount(mixed $query, SortDirection $sortDirection): Builder
    {
        return $query->selectRaw('("attributes"->>\'validatorVoteBalance\')::numeric AS vote_count')
            ->selectRaw('wallets.*')
            ->orderByRaw('CASE WHEN NULLIF(("attributes"->>\'validatorVoteBalance\')::numeric, 0) IS NULL THEN 1 ELSE 0 END ASC')
            ->orderByRaw(sprintf(
                '("attributes"->>\'validatorVoteBalance\')::numeric %s',
                $sortDirection->value
            ))
            ->orderByRaw('("attributes"->>\'validatorRank\')::numeric ASC');
    }

    public function scopeSortByNumberOfVoters(mixed $query, SortDirection $sortDirection): Builder
    {
        $voterCounts = (new ValidatorCache())->getAllVoterCounts();
        if (count($voterCounts) === 0) {
            return $query->selectRaw('0 AS no_of_voters')
                ->selectRaw('wallets.*');
        }

        return $query->selectRaw('voting_stats.count AS no_of_voters')
            ->selectRaw('wallets.*')
            ->join(DB::raw(sprintf(
                '(values %s) as voting_stats (address, count)',
                collect($voterCounts)
                    ->map(fn ($count, $address) => sprintf('(\'%s\',%d)', $address, $count))
                    ->join(','),
            )), 'wallets.address', '=', 'voting_stats.address', 'left outer')
            ->orderByRaw(sprintf('no_of_voters %s NULLS LAST', $sortDirection->value))
            ->orderByRaw('("attributes"->>\'validatorRank\')::numeric ASC');
    }

    public function scopeSortByMissedBlocks(mixed $query, SortDirection $sortDirection): Builder
    {
        $missedBlocks = ForgingStats::selectRaw('address, COUNT(*) as count')
            ->groupBy('address')
            ->whereNot('missed_height', null)
            ->get();

        if (count($missedBlocks) === 0) {
            return $query->selectRaw('0 AS missed_blocks')
                ->selectRaw('wallets.*');
        }

        return $query->selectRaw('COALESCE(forging_stats.count, 0) AS missed_blocks')
            ->selectRaw('wallets.*')
            ->join(DB::raw(sprintf(
                '(values %s) as forging_stats (address, count)',
                $missedBlocks->map(fn ($forgingStat) => sprintf('(\'%s\',%d)', $forgingStat->address, $forgingStat->count))
                    ->join(','),
            )), 'wallets.address', '=', 'forging_stats.address', 'left outer')
            ->when($sortDirection === SortDirection::ASC, fn ($query) => $query->orderByRaw(sprintf(
                'CASE WHEN ("attributes"->>\'validatorRank\')::numeric <= %d THEN 0 ELSE 1 END ASC',
                Network::validatorCount(),
            )))
            ->orderByRaw(sprintf(
                'missed_blocks %s',
                $sortDirection->value,
            ))
            ->orderByRaw('("attributes"->>\'validatorRank\')::numeric ASC');
    }
}
