<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates;

use App\Enums\CoreTransactionTypeEnum;
use App\Models\Transaction;

final class LargestTransactionAggregate
{
    public function aggregate(): ?Transaction
    {
        $transfer     = Transaction::where('type', CoreTransactionTypeEnum::TRANSFER)->orderBy('amount', 'desc')->limit(1)->first();
        $multipayment = Transaction::query()->select('*')->where('type', CoreTransactionTypeEnum::MULTI_PAYMENT)->from(function ($query) {
            $query->from('transactions', 't')
                ->select([
                    't.*',
                    'multipayment_amount' => function ($query) {
                        $query->selectRaw('SUM(MP_AMOUNT)')
                            ->from(function ($query) {
                                $query->selectRaw('(jsonb_array_elements(mpt.asset->\'payments\')->>\'amount\')::numeric as MP_AMOUNT')
                                    ->from('transactions', 'mpt')
                                    ->whereColumn('mpt.id', 't.id');
                            }, 'b');
                    },
                ]);
        }, 't')
        ->orderBy('multipayment_amount', 'desc')
        ->limit(1)
        ->first();

        if ($transfer['amount'] >= $multipayment['multipayment_amount']) {
            return $transfer;
        }

        return $multipayment;
    }
}
