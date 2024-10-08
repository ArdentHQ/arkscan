<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Historical;

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
            'amount' => (int) round(($data['amount'] / config('currencies.notation.crypto', 1e18)) / $daysSinceEpoch),
            'fee'    => (int) round(($data['fee'] / config('currencies.notation.crypto', 1e18)) / $daysSinceEpoch),
        ];
    }
}
