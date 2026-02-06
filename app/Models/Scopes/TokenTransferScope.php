<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Enums\ContractMethod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

final class TokenTransferScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder
            ->where(DB::raw('SUBSTRING(encode(transactions.data, \'hex\'), 1, 8)'), ContractMethod::transfer());
    }
}
