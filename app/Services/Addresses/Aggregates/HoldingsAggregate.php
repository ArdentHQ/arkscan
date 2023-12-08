<?php

declare(strict_types=1);

namespace App\Services\Addresses\Aggregates;

use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

final class HoldingsAggregate
{
    /**
     * @return array<int, array{'grouped': int, 'count': int}>
     */
    public function aggregate(): array
    {
        return Wallet::query()
            ->select(DB::raw(
                'CASE WHEN balance > 1000000*1e8 THEN 1000000
                WHEN balance > 100000*1e8 THEN 100000
                WHEN balance > 10000*1e8 THEN 10000
                WHEN balance > 1000*1e8 THEN 1000
                WHEN balance > 1*1e8 THEN 1
                ELSE 0 END AS grouped,
                count(*)'
            ))
            ->groupBy('grouped')
            ->orderBy('grouped', 'asc')
            ->get();
    }
}
