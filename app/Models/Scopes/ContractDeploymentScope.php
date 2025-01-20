<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class ContractDeploymentScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('recipient_address', null);
    }
}
