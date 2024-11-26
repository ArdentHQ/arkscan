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
            ->where('data', '!=', null)
            ->where('data', '!=', '')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('recipient_address', '!=', Network::knownContract('consensus'));
                })
                ->orWhere(
                    fn ($query) => $query->where('recipient_address', Network::knownContract('consensus'))
                        ->whereNotIn(DB::raw('SUBSTRING(encode(data, \'hex\'), 1, 8)'), [
                            ContractMethod::transfer(),
                            ContractMethod::vote(),
                            ContractMethod::unvote(),
                            ContractMethod::validatorRegistration(),
                            ContractMethod::validatorResignation(),
                        ])
                );
            });
    }
}
