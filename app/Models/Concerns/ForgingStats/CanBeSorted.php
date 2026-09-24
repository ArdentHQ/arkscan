<?php

declare(strict_types=1);

namespace App\Models\Concerns\ForgingStats;

use App\Enums\SortDirection;
use App\Models\ForgingStats;
use App\Models\Wallet;
use App\Services\Cache\DelegateCache;
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

        $delegateNames = Wallet::whereIn('public_key', $missedBlockPublicKeys)
            ->get()
            ->pluck('attributes.delegate.username', 'public_key');

        if (count($delegateNames) === 0) {
            return $query->selectRaw('NULL AS delegate_name')
                ->selectRaw('forging_stats.*');
        }

        $valuesList = implode(',', array_fill(0, count($delegateNames), '(?,?)'));

        $bindings = $delegateNames->flatMap(fn ($name, $publicKey) => [$publicKey, $name])->all();

        $query->join(DB::raw("(values {$valuesList}) as wallets (public_key, name)"), 'forging_stats.public_key', '=', 'wallets.public_key', 'left outer');
        $query->getQuery()->addBinding($bindings, 'join');

        return $query->selectRaw('wallets.name AS delegate_name')
            ->selectRaw('forging_stats.*')
            ->orderByRaw('delegate_name '.$sortDirection->value.', timestamp DESC');
    }

    public function scopeSortByVoteCount(mixed $query, SortDirection $sortDirection): Builder
    {
        $missedBlockPublicKeys = ForgingStats::groupBy('public_key')->pluck('public_key');

        $delegateVotes = Wallet::whereIn('public_key', $missedBlockPublicKeys)
            ->get()
            ->pluck('attributes.delegate.voteBalance', 'public_key');

        if (count($delegateVotes) === 0) {
            return $query->selectRaw('0 AS votes')
                ->selectRaw('forging_stats.*');
        }

        $valuesList = implode(',', array_fill(0, count($delegateVotes), '(?, CAST(? AS bigint))'));

        $bindings = $delegateVotes->flatMap(fn ($votes, $publicKey) => [$publicKey, $votes])->all();

        $query->join(DB::raw("(values {$valuesList}) as wallets (public_key, votes)"), 'forging_stats.public_key', '=', 'wallets.public_key', 'left outer');
        $query->getQuery()->addBinding($bindings, 'join');

        return $query->selectRaw('wallets.votes AS votes')
            ->selectRaw('forging_stats.*')
            ->orderByRaw('votes '.$sortDirection->value.', timestamp DESC');
    }

    public function scopeSortByNumberOfVoters(mixed $query, SortDirection $sortDirection): Builder
    {
        $voterCounts = (new DelegateCache())->getAllVoterCounts();
        if (count($voterCounts) === 0) {
            return $query->selectRaw('0 AS no_of_voters')
                ->selectRaw('forging_stats.*');
        }

        $voterCounts = collect($voterCounts);

        $valuesList = implode(',', array_fill(0, count($voterCounts), '(?, CAST(? AS bigint))'));

        $bindings = $voterCounts->flatMap(fn ($count, $publicKey) => [$publicKey, $count])->all();

        $query->join(DB::raw("(values {$valuesList}) as voting_stats (public_key, count)"), 'forging_stats.public_key', '=', 'voting_stats.public_key', 'left outer');
        $query->getQuery()->addBinding($bindings, 'join');

        return $query->selectRaw('voting_stats.count AS no_of_voters')
            ->selectRaw('forging_stats.*')
            ->orderByRaw(sprintf('no_of_voters %s NULLS LAST, timestamp DESC', $sortDirection->value));
    }
}
