<?php

declare(strict_types=1);

namespace App\Models\Concerns\ForgingStats;

use App\Enums\SortDirection;
use App\Models\ForgingStats;
use App\Models\Wallet;
use App\Services\Cache\ValidatorCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait CanBeSorted
{
    public function scopeSortByHeight(mixed $query, SortDirection $sortDirection): Builder
    {
        return $query->orderByRaw('missed_height '.$sortDirection->value.', timestamp DESC');
    }

    public function scopeSortByAge(mixed $query, SortDirection $sortDirection): Builder
    {
        return $query->orderByRaw('timestamp '.$sortDirection->value);
    }

    public function scopeSortByUsername(mixed $query, SortDirection $sortDirection): Builder
    {
        $missedBlockPublicKeys = ForgingStats::groupBy('address')->pluck('address');

        $validatorNames = Wallet::whereIn('address', $missedBlockPublicKeys)
            ->get()
            ->pluck('attributes.username', 'address');

        if (count($validatorNames) === 0) {
            return $query->selectRaw('NULL AS validator_name')
                ->selectRaw('forging_stats.*');
        }

        return $query->selectRaw('wallets.name AS validator_name')
            ->selectRaw('forging_stats.*')
            ->join(DB::raw(sprintf(
                '(values %s) as wallets (address, name)',
                $validatorNames->map(fn ($name, $publicKey) => sprintf('(\'%s\',\'%s\')', $publicKey, $name))
                    ->join(','),
            )), 'forging_stats.address', '=', 'wallets.address', 'left outer')
            ->orderByRaw('validator_name '.$sortDirection->value.', timestamp DESC');
    }

    public function scopeSortByVoteCount(mixed $query, SortDirection $sortDirection): Builder
    {
        $missedBlockAddresses = ForgingStats::groupBy('address')->pluck('address');

        $validatorVotes = Wallet::whereIn('address', $missedBlockAddresses)
            ->get()
            ->pluck('attributes.validatorVoteBalance', 'address');

        if (count($validatorVotes) === 0) {
            return $query->selectRaw('0 AS votes')
                ->selectRaw('forging_stats.*')
                // since votes is 0, we just sort by timestamp
                ->orderByRaw('timestamp '.$sortDirection->value);
        }

        return $query->selectRaw('wallets.votes AS votes')
            ->selectRaw('forging_stats.*')
            ->join(DB::raw(sprintf(
                '(values %s) as wallets (address, votes)',
                $validatorVotes->map(fn ($votes, $address) => sprintf('(\'%s\', CAST(%s AS NUMERIC))', $address, $votes))
                    ->join(',')
            )), 'forging_stats.address', '=', 'wallets.address', 'left outer')
            ->orderByRaw('votes '.$sortDirection->value.', timestamp DESC');
    }

    public function scopeSortByNumberOfVoters(mixed $query, SortDirection $sortDirection): Builder
    {
        $voterCounts = (new ValidatorCache())->getAllVoterCounts();

        if (count($voterCounts) === 0) {
            return $query->selectRaw('0 AS no_of_voters')
                ->selectRaw('forging_stats.*')
                // since no_of_voters is 0, we just sort by timestamp
                ->orderByRaw('timestamp '.$sortDirection->value);
        }

        return $query->selectRaw('voting_stats.count AS no_of_voters')
            ->selectRaw('forging_stats.*')
            ->join(DB::raw(sprintf(
                '(values %s) as voting_stats (address, count)',
                collect($voterCounts)
                    ->map(fn ($count, $address) => sprintf('(\'%s\',%d)', $address, $count))
                    ->join(','),
            )), 'forging_stats.address', '=', 'voting_stats.address', 'left outer')
            ->orderByRaw(sprintf('no_of_voters %s NULLS LAST, timestamp DESC', $sortDirection->value));
    }
}
