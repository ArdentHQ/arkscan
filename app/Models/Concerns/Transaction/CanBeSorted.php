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
        return $query->join('wallets', 'wallets.address', '=', 'transactions.from')
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
                        ->where('to', Network::knownContract('consensus'))
                        ->whereColumn('transactions.hash', 'validator_transaction.hash')
                        ->from('transactions', 'validator_transaction');
                }, 'validator_vote'),
        ])
        ->selectRaw('transactions.*')
        ->orderBy('transaction_type', $sortDirection->value);
    }

    public function scopeSortByUsername(mixed $query, SortDirection $sortDirection): Builder
    {
        $knownWallets = collect(Network::knownWallets())
            ->pluck('name', 'address');

        return $query
            ->select([
                'validator_name' => fn ($query) => $query
                    ->from(function ($query) {
                        $query
                            ->selectRaw("CONCAT('0x', RIGHT(SUBSTRING(encode(data, 'hex'), 9), 40)) as vote")
                            ->where('to', Network::knownContract('consensus'))
                            ->whereRaw("SUBSTRING(encode(data, 'hex'), 1, 8) = ?", [ContractMethod::vote()])
                            ->whereColumn('transactions.hash', 'validator_transaction.hash')
                            ->from('transactions', 'validator_transaction');
                    }, 'validator_vote')
                    ->join('wallets', DB::raw('LOWER(wallets.address)'), '=', DB::raw('LOWER(validator_vote.vote)'))
                    ->when(
                        $knownWallets->isEmpty(),
                        fn ($query) => $query
                        ->selectRaw("
                            COALESCE(
                                NULLIF(TRIM(BOTH '\"' FROM wallets.attributes->>'username'), ''),
                                wallets.address
                            )
                        ")
                    )
                    ->when(
                        $knownWallets->isNotEmpty(),
                        fn ($query) => $query
                        ->selectRaw("
                            COALESCE(
                                known_wallets.name,
                                NULLIF(TRIM(BOTH '\"' FROM wallets.attributes->>'username'), ''),
                                wallets.address
                            )
                        ")
                        ->join(DB::raw(sprintf(
                            '(VALUES %s) as known_wallets (address, name)',
                            $knownWallets->map(fn ($address, $name) => sprintf("('%s','%s')", $address, $name))->join(',')
                        )), DB::raw('LOWER(known_wallets.address)'), '=', DB::raw("LOWER(CONCAT('0x', RIGHT(SUBSTRING(encode(data, 'hex'), 9), 40)))"), 'left outer')
                    ),

                'transaction_type' => fn ($query) => $query
                    ->selectRaw('COALESCE(validator_vote.vote, validator_vote.unvote)')
                    ->from(function ($query) {
                        $query
                            ->selectRaw('CASE WHEN (SUBSTRING(encode(data, \'hex\'), 1, 8) = ?) THEN 1 END as unvote', [ContractMethod::unvote()])
                            ->selectRaw('CASE WHEN (SUBSTRING(encode(data, \'hex\'), 1, 8) = ?) THEN 0 END as vote', [ContractMethod::vote()])
                            ->where('to', Network::knownContract('consensus'))
                            ->whereColumn('transactions.hash', 'validator_transaction.hash')
                            ->from('transactions', 'validator_transaction');
                    }, 'validator_vote'),
            ])
            ->selectRaw('transactions.*')
            ->orderBy('transaction_type', 'asc')
            ->orderBy('validator_name', $sortDirection->value);
    }
}
