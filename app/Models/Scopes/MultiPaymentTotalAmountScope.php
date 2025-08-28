<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

final class MultiPaymentTotalAmountScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->joinSubLateral(function ($query) {
            $query->selectRaw('SUM(multi_payments.amount) as recipient_amount')
                ->selectRaw('TRUE as is_multipayment')
                ->from('multi_payments')
                ->whereColumn('multi_payments.hash', 'transactions.hash')
                ->groupBy('multi_payments.hash');
        }, 'recipient_amounts', DB::raw('true'), '=', DB::raw('true'), 'left outer');
    }
}
