<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Cache\ValidatorCache;
use App\Services\Monitor\Aggregates\ValidatorTotalAggregates;
use Illuminate\Console\Command;

final class CacheValidatorAggregates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-validator-aggregates';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Cache expensive aggregation data for all validators.';

    public function handle(ValidatorCache $cache): void
    {
        $aggregate = (new ValidatorTotalAggregates())->aggregate();

        $cache->setTotalFees($aggregate->pluck('fee', 'proposer')->toArray());

        $cache->setTotalRewards($aggregate->pluck('reward', 'proposer')->toArray());

        $cache->setTotalBlocks($aggregate->pluck('count', 'proposer')->toArray());
    }
}
