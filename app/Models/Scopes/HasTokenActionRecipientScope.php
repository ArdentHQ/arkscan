<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class HasTokenActionRecipientScope implements Scope
{
    public function __construct(private string $address)
    {
        //
    }

    public function apply(Builder $builder, Model $model)
    {
        $builder->whereExists(function ($query) {
            $query->selectRaw('1')
                ->from('token_actions')
                ->whereColumn('token_actions.transaction_hash', 'transactions.hash')
                ->where('token_actions.to', $this->address);
        });
    }
}
