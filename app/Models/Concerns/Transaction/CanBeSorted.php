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
                ->selectRaw('coalesce(validator_vote.vote, validator_vote.unvote)')
                ->from(function ($query) {
                    $query
                        ->selectRaw('case when (NULLIF(LEFT(asset->\'votes\'->>0, 1), \'-\') IS null) then 0 end as unvote')
                        ->selectRaw('case when (NULLIF(LEFT(asset->\'votes\'->>0, 1), \'+\') IS null) then 1 end as vote')
                        ->whereColumn('transactions.id', 'validator_transaction.id')
                        ->from('transactions', 'validator_transaction');
                }, 'validator_vote'),
        ])
        ->selectRaw('transactions.*')
        ->orderBy('transaction_type', $sortDirection->value);
    }
}
