<?php

declare(strict_types=1);

namespace App\Services\Addresses\Aggregates;

use App\Models\Wallet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class HoldingsAggregate
{
    /**
     * @return Collection<int, array{'grouped': int, 'count': int}>
     */
    public function aggregate(): ?Collection
    {
        // @phpstan-ignore-next-line
        return Wallet::query()
            ->select(DB::raw(sprintf(
                'CASE WHEN balance > 1000000 * 1e%d THEN 1000000
                WHEN balance > 100000 * 1e%1$d THEN 100000
                WHEN balance > 10000 * 1e%1$d THEN 10000
                WHEN balance > 1000 * 1e%1$d THEN 1000
                WHEN balance > 1 * 1e%1$d THEN 1
                ELSE 0 END AS grouped,
                count(*)',
                config('currencies.decimals.crypto', 18)
            )))
            ->groupBy('grouped')
            ->orderBy('grouped', 'asc')
            ->get();
    }
}
