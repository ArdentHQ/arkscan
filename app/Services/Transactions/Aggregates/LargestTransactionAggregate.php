<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

final class LargestTransactionAggregate
{
    public function aggregate(): Transaction
    {
        $subquery = Transaction::select(DB::raw('id, jsonb_array_elements(asset->\'payments\')->>\'amount\' as multipayment_amount'));

        return Transaction::select(DB::raw('t.*, amount + sum(multipayment_amount::bigint) as total_amount'))
            ->from('transactions', 't')
            ->join(DB::raw('('.$subquery->toSql().') AS d'), 'd.id', '=', 't.id')
            ->groupBy('t.id')
            ->orderBy('total_amount', 'desc')
            ->limit(1)
            ->first();
    }
}
