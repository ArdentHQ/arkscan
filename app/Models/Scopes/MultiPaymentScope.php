<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Enums\ContractMethod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class MultiPaymentScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->withScope(ContractScope::class, ContractMethod::multiPayment());
    }
}
