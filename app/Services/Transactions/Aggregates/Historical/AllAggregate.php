<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Historical;

use App\Facades\Network;
use App\Models\Transaction;
use Illuminate\Support\Collection;

final class AllAggregate
{
    public function aggregate(): Collection
    {
        return Transaction::query()
            ->selectRaw('MAX(timestamp) as timestamp')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("to_char(to_timestamp(? + timestamp) AT TIME ZONE 'UTC', ?) as formatted_date", [Network::epoch()->timestamp, 'YYYY-MM'])
            ->orderBy('formatted_date')
            ->groupBy('formatted_date')
            ->pluck('total', 'formatted_date');
    }
}
