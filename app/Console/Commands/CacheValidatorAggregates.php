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

        $cache->setTotalAmounts($aggregate->pluck('total_amount', 'generator_address')->toArray());

        $cache->setTotalFees($aggregate->pluck('total_fee', 'generator_address')->toArray());

        $cache->setTotalRewards($aggregate->pluck('reward', 'generator_address')->toArray());

        $cache->setTotalBlocks($aggregate->pluck('count', 'generator_address')->toArray());
    }
}
