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
        $missedBlockPublicKeys = ForgingStats::groupBy('public_key')->pluck('public_key');

        $validatorNames = Wallet::whereIn('public_key', $missedBlockPublicKeys)
            ->get()
            ->pluck('attributes.username', 'public_key');

        if (count($validatorNames) === 0) {
            return $query->selectRaw('NULL AS validator_name')
                ->selectRaw('forging_stats.*');
        }

        return $query->selectRaw('wallets.name AS validator_name')
            ->selectRaw('forging_stats.*')
            ->join(DB::raw(sprintf(
                '(values %s) as wallets (public_key, name)',
                $validatorNames->map(fn ($name, $publicKey) => sprintf('(\'%s\',\'%s\')', $publicKey, $name))
                    ->join(','),
            )), 'forging_stats.public_key', '=', 'wallets.public_key', 'left outer')
            ->orderByRaw('validator_name '.$sortDirection->value.', timestamp DESC');
    }

    public function scopeSortByVoteCount(mixed $query, SortDirection $sortDirection): Builder
    {
        $missedBlockPublicKeys = ForgingStats::groupBy('public_key')->pluck('public_key');

        $validatorVotes = Wallet::whereIn('public_key', $missedBlockPublicKeys)
            ->get()
            ->pluck('attributes.validatorVoteBalance', 'public_key');

        if (count($validatorVotes) === 0) {
            return $query->selectRaw('0 AS votes')
                ->selectRaw('forging_stats.*');
        }

        return $query->selectRaw('wallets.votes AS votes')
            ->selectRaw('forging_stats.*')
            ->join(DB::raw(sprintf(
                '(values %s) as wallets (public_key, votes)',
                $validatorVotes->map(fn ($votes, $publicKey) => sprintf('(\'%s\',%d)', $publicKey, $votes))
                    ->join(','),
            )), 'forging_stats.public_key', '=', 'wallets.public_key', 'left outer')
            ->orderByRaw('votes '.$sortDirection->value.', timestamp DESC');
    }

    public function scopeSortByNumberOfVoters(mixed $query, SortDirection $sortDirection): Builder
    {
        $voterCounts = (new ValidatorCache())->getAllVoterCounts();
        if (count($voterCounts) === 0) {
            return $query->selectRaw('0 AS no_of_voters')
                ->selectRaw('forging_stats.*');
        }

        return $query->selectRaw('voting_stats.count AS no_of_voters')
            ->selectRaw('forging_stats.*')
            ->join(DB::raw(sprintf(
                '(values %s) as voting_stats (public_key, count)',
                collect($voterCounts)
                    ->map(fn ($count, $publicKey) => sprintf('(\'%s\',%d)', $publicKey, $count))
                    ->join(','),
            )), 'forging_stats.public_key', '=', 'voting_stats.public_key', 'left outer')
            ->orderByRaw(sprintf('no_of_voters %s NULLS LAST, timestamp DESC', $sortDirection->value));
    }
}
