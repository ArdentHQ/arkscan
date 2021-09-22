<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Enums\MagistrateTransactionEntityActionEnum;
use App\Enums\MagistrateTransactionEntityTypeEnum;
use App\Enums\MagistrateTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class ModuleEntityUpdateScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('type_group', TransactionTypeGroupEnum::MAGISTRATE);
        $builder->where('type', MagistrateTransactionTypeEnum::ENTITY);
        $builder->where('asset->type', MagistrateTransactionEntityTypeEnum::MODULE);
        $builder->where('asset->action', MagistrateTransactionEntityActionEnum::UPDATE);
    }
}
