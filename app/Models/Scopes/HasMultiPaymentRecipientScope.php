<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

final class HasMultiPaymentRecipientScope implements Scope
{
    public function __construct(private string $address)
    {
        //
    }

    public function apply(Builder $builder, Model $model)
    {
        $builder->withScope(MultiPaymentScope::class)
            ->whereIn(
                DB::raw("'".strtolower($this->address)."'"),
                function ($query) {
                    $query->selectRaw('LOWER(CONCAT(\'0x\', RIGHT(encode(substring(transactions.data, 69 + (n * 32), 32), \'hex\'), 40)))')
                        ->from(DB::raw('generate_series(1, CAST(encode(SUBSTRING(transactions.data, 69, 32), \'hex\') AS int)) n'))
                        ->whereColumn('transactions.id', 'transactions.id');
                }
            );
    }
}
