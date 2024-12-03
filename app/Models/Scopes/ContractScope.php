<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Facades\Network;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class ContractScope implements Scope
{
    private string $contract;

    public function __construct(string $contract)
    {
        $this->contract = $contract;
    }

    public function apply(Builder $builder, Model $model)
    {
        $builder->where('recipient_address', Network::knownContract('consensus'))
            ->whereRaw('SUBSTRING(encode(data, \'hex\'), 1, 8) = ?', [$this->contract]);
    }
}
