<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class HasTokenTransferRecipientScope implements Scope
{
    public function __construct(private string $address)
    {
        //
    }

    public function apply(Builder $builder, Model $model)
    {
        $builder->whereExists(function ($query) {
            $query->selectRaw('1')
                ->from('token_transfers')
                ->whereColumn('token_transfers.transaction_hash', 'transactions.hash')
                ->where('token_transfers.to', $this->address);
        });
    }
}
