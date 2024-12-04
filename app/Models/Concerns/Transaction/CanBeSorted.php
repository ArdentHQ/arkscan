<?php

declare(strict_types=1);

namespace App\Models\Concerns\Transaction;

use App\Enums\ContractMethod;
use App\Enums\SortDirection;
use App\Facades\Network;
use Illuminate\Database\Eloquent\Builder;

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
                        ->selectRaw('case when (SUBSTRING(encode(data, \'hex\'), 1, 8) = ?) then 1 end as unvote', [ContractMethod::unvote()])
                        ->selectRaw('case when (SUBSTRING(encode(data, \'hex\'), 1, 8) = ?) then 0 end as vote', [ContractMethod::vote()])
                        ->where('recipient_address', Network::knownContract('consensus'))
                        ->whereColumn('transactions.id', 'validator_transaction.id')
                        ->from('transactions', 'validator_transaction');
                }, 'validator_vote'),
        ])
        ->selectRaw('transactions.*')
        ->orderBy('transaction_type', $sortDirection->value);
    }
}
