<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Enums\TransactionTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class UsernameResignationScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('type', TransactionTypeEnum::USERNAME_RESIGNATION);
    }
}
