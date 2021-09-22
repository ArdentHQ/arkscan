<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Enums\CoreTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class TimelockScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('type_group', TransactionTypeGroupEnum::CORE);
        $builder->where('type', CoreTransactionTypeEnum::TIMELOCK);
    }
}
