<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class HasMultiPaymentRecipientScope implements Scope
{
    public function __construct(private string $address)
    {
        //
    }

    public function apply(Builder $builder, Model $model)
    {
        $builder->whereRaw('"multi_payment_recipients" @> ARRAY[?]::citext[]', [$this->address]);
    }
}
