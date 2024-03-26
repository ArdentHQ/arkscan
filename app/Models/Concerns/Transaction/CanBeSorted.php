<?php

declare(strict_types=1);

namespace App\Models\Concerns\Transaction;

use App\Enums\SortDirection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait CanBeSorted
{
    public function scopeSortByAge(mixed $query, SortDirection $sortDirection): Builder
    {
        return $query->orderBy('timestamp', $sortDirection->value);
    }

    public function scopeSortByAddress(mixed $query, SortDirection $sortDirection): Builder
    {
        return $query->join('wallets', 'wallets.public_key', '=', 'transactions.sender_public_key')
            ->orderBy('wallets.address', $sortDirection->value);
    }

    public function scopeSortByType(mixed $query, SortDirection $sortDirection): Builder
    {
        return $query->select([
            'transaction_type' => fn ($query) => $query
                ->selectRaw('coalesce(validator_vote.votecombination, validator_vote.vote, validator_vote.unvote)')
                ->from(function ($query) {
                    $query
                        ->selectRaw('case when (NULLIF(LEFT(asset->\'votes\'->>0, 1), \'-\') IS null) then 0 end as unvote')
                        ->selectRaw('case when (NULLIF(LEFT(asset->\'votes\'->>0, 1), \'+\') IS null) then 1 end as vote')
                        ->selectRaw('case when (NULLIF(LEFT(asset->\'votes\'->>0, 1), \'-\') IS null and asset->\'votes\'->>1 is not null and NULLIF(LEFT(asset->\'votes\'->>1, 1), \'+\') IS null) then 2 end as votecombination')
                        ->whereColumn('transactions.id', 'validator_transaction.id')
                        ->from('transactions', 'validator_transaction');
                }, 'validator_vote'),
        ])
        ->selectRaw('transactions.*')
        ->orderBy('transaction_type', $sortDirection->value);
    }

    public function scopeSortByUsername(mixed $query, SortDirection $sortDirection): Builder
    {
        return $query->select([
            'validator_name' => fn ($query) => $query
                ->selectRaw('wallets.attributes->\'username\'')
                ->from(function ($query) {
                    $query
                        ->selectRaw('case when (NULLIF(LEFT(asset->\'votes\'->>0, 1), \'-\') IS null) then substring(asset->\'votes\'->>0, 2) end as unvote')
                        ->selectRaw('case when (NULLIF(LEFT(asset->\'votes\'->>0, 1), \'+\') IS null) then substring(asset->\'votes\'->>0, 2) end as vote')
                        ->selectRaw('case when (NULLIF(LEFT(asset->\'votes\'->>0, 1), \'-\') IS null and asset->\'votes\'->>1 is not null and NULLIF(LEFT(asset->\'votes\'->>1, 1), \'+\') IS null) then substring(asset->\'votes\'->>1, 2) end as votecombination')
                        ->whereColumn('transactions.id', 'validator_transaction.id')
                        ->from('transactions', 'validator_transaction');
                }, 'validator_vote')
                ->join('wallets', 'wallets.public_key', '=', DB::raw('coalesce(validator_vote.votecombination, validator_vote.vote, validator_vote.unvote)')),
        ])
        ->selectRaw('transactions.*')
        ->orderBy('validator_name', $sortDirection->value);
    }
}
