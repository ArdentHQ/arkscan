<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Enums\PayloadSignature;
use App\Facades\Network;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class ContractScope implements Scope
{
    private string $contract;

    public function __construct(PayloadSignature | string $contract)
    {
        if ($contract instanceof PayloadSignature) {
            $contract = $contract->value;
        }

        $this->contract = $contract;
    }

    public function apply(Builder $builder, Model $model)
    {
        return $builder->where('recipient_address', Network::knownContract('consensus'))
            ->whereRaw('SUBSTRING(encode(data, \'hex\'), 1, 8) = ?', [$this->contract]);
    }
}
