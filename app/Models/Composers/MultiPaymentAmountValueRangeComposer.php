<?php

declare(strict_types=1);

namespace App\Models\Composers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

final class MultiPaymentAmountValueRangeComposer
{
    public static function compose(Builder $query, array $parameters): Builder
    {
        $from = Arr::get($parameters, 'amountFrom');
        $to   = Arr::get($parameters, 'amountTo');

        if (is_null($from) && is_null($to)) {
            return $query;
        }

        $query->where('amount', '=', 0);

        $query->whereExists(function (\Illuminate\Database\Query\Builder $query) use ($to, $from): void {
            $query->selectRaw('i.id')
                ->fromRaw("( SELECT id, (jsonb_array_elements(asset -> 'payments') ->> 'amount')::bigint am FROM transactions t WHERE t.id = id ) i")
                ->whereRaw('i.id = transactions.id')
                ->groupBy('i.id');

            if (! is_null($from) && $from > 0) {
                $query->havingRaw('sum(am) >= ?', [$from * 1e8]);
            }

            if (! is_null($to) && $to > 0) {
                $query->havingRaw('sum(am) <= ?', [$to * 1e8]);
            }
        });

        return $query;
    }
}
