<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Facades\Network;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class ActiveDelegateScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->whereNotNull('attributes->delegate->username');
        $builder->whereRaw("(\"attributes\"->'delegate'->>'rank')::numeric <= ?", [Network::delegateCount()]);
        $builder->orderByRaw("(\"attributes\"->'delegate'->>'rank')::numeric ASC");
    }
}
