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
        return Transaction::query()
            ->select(DB::raw('COUNT(*) as transactions, to_char(to_timestamp(timestamp+'.Network::epoch()->timestamp."), 'YYYY-MM') as month"))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('transactions', 'month')
            ->mapWithKeys(fn ($transactions, $month) => [$month => $transactions]);
    }
}
