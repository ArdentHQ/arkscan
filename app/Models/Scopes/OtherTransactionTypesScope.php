<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Enums\ContractMethod;
use App\Facades\Network;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

final class OtherTransactionTypesScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder
            ->whereRaw('SUBSTRING(data FROM 1 FOR 4) != \'\'')
            ->where(function ($query) {
                $query->whereNotIn('to', Network::knownContracts())
                ->orWhere(
                    fn ($query) => $query->where('to', Network::knownContract('consensus'))
                        ->whereNotIn(DB::raw('SUBSTRING(encode(data, \'hex\'), 1, 8)'), [
                            ContractMethod::transfer(),
                            ContractMethod::multiPayment(),
                            ContractMethod::vote(),
                            ContractMethod::unvote(),
                            ContractMethod::validatorRegistration(),
                            ContractMethod::validatorResignation(),
                        ])
                );
            });
    }
}
