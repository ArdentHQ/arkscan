<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Cache\DelegateCache;
use App\Services\Monitor\Aggregates\DelegateTotalAggregates;
use Illuminate\Console\Command;

final class CacheDelegateAggregates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-delegate-aggregates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache expensive aggregation data for all delegates.';

    public function handle(DelegateCache $cache): void
    {
        $aggregate = (new DelegateTotalAggregates())->aggregate();

        $cache->setTotalAmounts(fn () => $aggregate->pluck('total_amount', 'generator_public_key')->toArray());

        $cache->setTotalFees(fn () => $aggregate->pluck('total_fee', 'generator_public_key')->toArray());

        $cache->setTotalRewards(fn () => $aggregate->pluck('reward', 'generator_public_key')->toArray());

        $cache->setTotalBlocks(fn () => $aggregate->pluck('count', 'generator_public_key')->toArray());
    }
}
