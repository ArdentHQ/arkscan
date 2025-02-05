<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class IsVoteForAddressScope implements Scope
{
    public function __construct(private string $address)
    {
        //
    }

    public function apply(Builder $builder, Model $model)
    {
        $builder->withScope(VoteScope::class)
            ->whereRaw('LOWER(CONCAT(\'0x\', RIGHT(SUBSTRING(encode(data, \'hex\'), 9), 40))) = ?', [strtolower($this->address)]);
    }
}
