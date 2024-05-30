<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Concerns\DispatchesStatisticsEvents;
use App\Events\Statistics\TransactionDetails;
use App\Services\Blocks\Aggregates\HighestBlockFeeAggregate;
use App\Services\Blocks\Aggregates\LargestBlockAggregate;
use App\Services\Blocks\Aggregates\MostTransactionsBlockAggregate;
use App\Services\Cache\BlockCache;
use Illuminate\Console\Command;

final class CacheBlocks extends Command
{
    use DispatchesStatisticsEvents;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-blocks';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Cache expensive block aggregates.';

    public function handle(BlockCache $cache): void
    {
        $largestBlockByAmount           = (new LargestBlockAggregate())->aggregate();
        $largestBlockByFees             = (new HighestBlockFeeAggregate())->aggregate();
        $largestBlockByTransactionCount = (new MostTransactionsBlockAggregate())->aggregate();

        if ($largestBlockByAmount !== null) {
            if ($cache->getLargestIdByAmount() !== $largestBlockByAmount->id) {
                $this->hasChanges = true;
            }

            $cache->setLargestIdByAmount($largestBlockByAmount->id);
        }

        if ($largestBlockByFees !== null) {
            if (! $this->hasChanges && $cache->getLargestIdByFees() !== $largestBlockByFees->id) {
                $this->hasChanges = true;
            }

            $cache->setLargestIdByFees($largestBlockByFees->id);
        }

        if ($largestBlockByTransactionCount !== null) {
            if (! $this->hasChanges && $cache->getLargestIdByTransactionCount() !== $largestBlockByTransactionCount->id) {
                $this->hasChanges = true;
            }

            $cache->setLargestIdByTransactionCount($largestBlockByTransactionCount->id);
        }

        $this->dispatchEvent(TransactionDetails::class);
    }
}
