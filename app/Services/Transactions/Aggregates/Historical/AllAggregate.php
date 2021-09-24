<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Historical;

use App\Facades\Network;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class AllAggregate
{
    public function aggregate(): Collection
    {
        $select = [
            'MAX(timestamp) as timestamp',
            'COUNT(*) as total',
            sprintf("to_char(to_timestamp(%d+timestamp) AT TIME ZONE 'UTC', '%s') as formatted_date", Network::epoch()->timestamp, 'YYYY-MM'),
        ];

        return Transaction::query()
            ->select(DB::raw(implode(', ', $select)))
            ->orderBy('formatted_date')
            ->groupBy('formatted_date')
            ->pluck('total', 'formatted_date');
    }
}
