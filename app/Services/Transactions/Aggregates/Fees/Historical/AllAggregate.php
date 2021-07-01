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
        return Transaction::query()
            ->select(DB::raw('SUM(fee) as fee, to_char(to_timestamp(timestamp+'.Network::epoch()->timestamp."), 'YYYY-MM') as month"))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('fee', 'month')
            ->mapWithKeys(fn ($fee, $month) => [$month => $fee->toFloat()]);
    }
}
