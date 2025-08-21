<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class HasMultiPaymentRecipientScope implements Scope
{
    public function __construct(private string $address)
    {
        //
    }

    public function apply(Builder $builder, Model $model)
    {
        $builder->whereExists(function ($query) {
            $query->selectRaw('1')
                ->from('multi_payments')
                ->whereColumn('transactions.hash', 'multi_payments.hash')
                ->where('multi_payments.to', $this->address);
        });
    }
}
