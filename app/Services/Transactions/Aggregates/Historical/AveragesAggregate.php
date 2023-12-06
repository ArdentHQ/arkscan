<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Historical;

use App\Enums\CoreTransactionTypeEnum;
use App\Services\Timestamp;
use Illuminate\Support\Facades\DB;

final class AveragesAggregate
{
    public function aggregate(): array
    {
        $data = DB::connection('explorer')
            ->query()
            ->select([
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as amount'),
                DB::raw('SUM(fee) as fee'),
                'multipayments' => function ($query) {
                    return $query
                        ->select(DB::raw('SUM(amount::bigint)'))
                        ->from(function ($query) {
                            return $query->selectRaw('jsonb_array_elements(asset->\'payments\')->>\'amount\' as amount')
                                ->from('transactions')
                                ->where('type', CoreTransactionTypeEnum::MULTI_PAYMENT);
                        }, 'payments');
                }
            ])
            ->from('transactions')
            ->first();

        $daysSinceEpoch = Timestamp::daysSinceEpoch();

        return [
            'count' => (int) round($data->count / $daysSinceEpoch),
            'amount' => (int) round(($data->amount + $data->multipayments) / 1e8 / $daysSinceEpoch),
            'fee' => (int) round(($data->fee / 1e8) / $daysSinceEpoch),
        ];
    }
}
