<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Blocks\Aggregates\HighestBlockFeeAggregate;
use App\Services\Blocks\Aggregates\LargestBlockAggregate;
use App\Services\Blocks\Aggregates\MostTransactionsBlockAggregate;
use App\Services\Cache\BlockCache;
use Illuminate\Console\Command;

final class CacheBlocks extends Command
{
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
        $largestBlockByAmount = (new LargestBlockAggregate())->aggregate();
        $largestBlockByFees = (new HighestBlockFeeAggregate())->aggregate();
        $largestBlockByTransactionCount = (new MostTransactionsBlockAggregate())->aggregate();

        if ($largestBlockByAmount !== null) {
            $cache->setLargestIdByAmount($largestBlockByAmount->id);
        }

        if ($largestBlockByFees !== null) {
            $cache->setLargestIdByFees($largestBlockByFees->id);
        }

        if ($largestBlockByTransactionCount !== null) {
            $cache->setLargestIdByTransactionCount($largestBlockByTransactionCount->id);
        }
    }
}
