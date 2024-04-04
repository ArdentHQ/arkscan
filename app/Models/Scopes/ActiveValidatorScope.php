<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Facades\Network;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class ActiveValidatorScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->whereNotNull('attributes->validator->username');
        $builder->whereRaw("(\"attributes\"->'validator'->>'rank')::numeric <= ?", [Network::validatorCount()]);
        $builder->orderByRaw("(\"attributes\"->'validator'->>'rank')::numeric ASC");
    }
}
