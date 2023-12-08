<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Addresses\Aggregates\HoldingsAggregate;
use App\Services\Cache\Statistics;
use Illuminate\Console\Command;

class CacheAddressStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-address-statistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache expensive address statistics';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Statistics $cache)
    {
        $holdings = (new HoldingsAggregate())->aggregate();

        if ($holdings !== null) {
            $cache->setAddressHoldings($holdings->toArray());
        }
    }
}
