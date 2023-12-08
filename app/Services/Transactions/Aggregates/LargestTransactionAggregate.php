<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates;

use App\Models\Transaction;

final class LargestTransactionAggregate
{
    public function aggregate(): ?Transaction
    {
        return Transaction::query()
            ->select('*')
            ->selectRaw('COALESCE(multipayment_volume, 0) + amount AS total_amount')
            ->from(function ($query) {
                $query->from('transactions', 't')
                    ->select([
                        't.*',
                        'multipayment_volume' => function ($query) {
                            $query->selectRaw('SUM(MP_AMOUNT)')
                                ->from(function ($query) {
                                    $query->selectRaw('(jsonb_array_elements(mpt.asset->\'payments\')->>\'amount\')::numeric as MP_AMOUNT')
                                        ->from('transactions', 'mpt')
                                        ->whereColumn('mpt.id', 't.id');
                                }, 'b');
                        },
                    ]);
            }, 't')
            ->orderBy('total_amount', 'desc')
            ->limit(1)
            ->first();
    }
}
