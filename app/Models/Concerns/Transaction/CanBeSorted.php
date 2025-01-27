<?php

declare(strict_types=1);

namespace App\Models\Concerns\Transaction;

use App\Enums\ContractMethod;
use App\Enums\SortDirection;
use App\Facades\Network;
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
        return $query->join('wallets', 'wallets.address', '=', 'transactions.sender_address')
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

    public function scopeSortByUsername(mixed $query, SortDirection $sortDirection): Builder
    {
        return $query
            ->select([
                'validator_name' => fn ($query) => $query
                    ->selectRaw('coalesce((wallets.attributes->\'username\')::text, wallets.address)')
                    ->from(function ($query) {
                        $query
                            ->selectRaw('CONCAT(\'0x\', RIGHT(SUBSTRING(encode(data, \'hex\'), 9), 40)) as vote')
                            ->where('recipient_address', Network::knownContract('consensus'))
                            ->whereRaw('SUBSTRING(encode(data, \'hex\'), 1, 8) = ?', [ContractMethod::vote()])
                            ->whereColumn('transactions.id', 'validator_transaction.id')
                            ->from('transactions', 'validator_transaction');
                    }, 'validator_vote')
                    ->join('wallets', DB::raw('LOWER(wallets.address)'), '=', DB::raw('LOWER(validator_vote.vote)')),

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
        ->orderBy('transaction_type', 'asc')
        ->orderBy('validator_name', $sortDirection->value);
    }
}
