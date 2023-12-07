<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Blocks\Aggregates\HighestBlockFeeAggregate;
use App\Services\Cache\BlockCache;
use App\Services\Blocks\Aggregates\LargestBlockAggregate;
use App\Services\Blocks\Aggregates\MostTransactionsBlockAggregate;
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
        $cache->setLargestIdByAmount((new LargestBlockAggregate())->aggregate()->id);
        $cache->setLargestIdByFees((new HighestBlockFeeAggregate())->aggregate()->id);
        $cache->setLargestIdByTransactionCount((new MostTransactionsBlockAggregate())->aggregate()->id);
    }
}
