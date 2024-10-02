<?php

declare(strict_types=1);

namespace App\Jobs\Webhooks;

use App\Events\Statistics\TransactionDetails;
use App\Services\Cache\TransactionCache;
use App\Services\Transactions\Aggregates\LargestTransactionAggregate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

final class CheckLargestTransaction implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function handle(): void
    {
        $cache              = new TransactionCache();
        $largestTransaction = (new LargestTransactionAggregate())->aggregate();
        if ($largestTransaction === null) {
            return;
        }

        if ($cache->getLargestIdByAmount() === $largestTransaction->id) {
            return;
        }

        $cache->setLargestIdByAmount($largestTransaction->id);

        TransactionDetails::dispatch();
    }
}
