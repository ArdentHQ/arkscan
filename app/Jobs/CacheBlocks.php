<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\Statistics\TransactionDetails;
use App\Services\Blocks\Aggregates\HighestBlockFeeAggregate;
use App\Services\Blocks\Aggregates\MostTransactionsBlockAggregate;
use App\Services\Cache\BlockCache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class CacheBlocks implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(BlockCache $cache): void
    {
        $largestBlockByFees             = (new HighestBlockFeeAggregate())->aggregate();
        $largestBlockByTransactionCount = (new MostTransactionsBlockAggregate())->aggregate();

        $hasChanges = false;
        if ($largestBlockByFees !== null) {
            if (! $hasChanges && $cache->getLargestIdByFees() !== $largestBlockByFees->hash) {
                $hasChanges = true;
            }

            $cache->setLargestIdByFees($largestBlockByFees->hash);
        }

        if ($largestBlockByTransactionCount !== null) {
            if (! $hasChanges && $cache->getLargestIdByTransactionCount() !== $largestBlockByTransactionCount->hash) {
                $hasChanges = true;
            }

            $cache->setLargestIdByTransactionCount($largestBlockByTransactionCount->hash);
        }

        if ($hasChanges) {
            TransactionDetails::dispatch();
        }
    }
}
