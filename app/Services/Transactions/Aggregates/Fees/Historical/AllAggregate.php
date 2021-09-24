<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Fees\Historical;

use App\Facades\Network;
use App\Models\Transaction;
use App\Services\Transactions\Aggregates\Concerns\HasPlaceholders;
use App\Services\Transactions\Aggregates\Concerns\HasQueries;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class AllAggregate
{
    use HasPlaceholders;
    use HasQueries;

    public function aggregate(): Collection
    {
        $select = [
            'SUM(fee) as fee',
            sprintf("to_char(to_timestamp(%d+timestamp) AT TIME ZONE 'UTC', '%s') as formatted_date", Network::epoch()->timestamp, 'YYYY-MM'),
        ];

        return Transaction::query()
            ->select(DB::raw(implode(', ', $select)))
            ->orderBy('formatted_date')
            ->groupBy('formatted_date')
            ->pluck('fee', 'formatted_date')
            ->mapWithKeys(fn ($fee, $month) => [$month => $fee->toFloat()]);
    }
}
