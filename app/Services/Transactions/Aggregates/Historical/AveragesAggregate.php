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
        $data = (array) DB::connection('explorer')
            ->query()
            ->select([
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as amount'),
                DB::raw('SUM(fee) as fee'),
            ])
            ->from('transactions')
            ->first();

        $daysSinceEpoch = Timestamp::daysSinceEpoch();

        return [
            'count'  => (int) round($data['count'] / $daysSinceEpoch),
            'amount' => (int) round(($data['amount'] / 1e8) / $daysSinceEpoch),
            'fee'    => (int) round(($data['fee'] / 1e8) / $daysSinceEpoch),
        ];
    }
}
