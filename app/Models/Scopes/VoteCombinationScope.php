<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Enums\TransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class VoteCombinationScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('type_group', TransactionTypeGroupEnum::CORE);
        $builder->where('type', TransactionTypeEnum::VOTE);
        $builder->whereJsonLength('asset->votes', 1);
        $builder->whereJsonLength('asset->unvotes', 1);
    }
}
