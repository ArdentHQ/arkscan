<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Cache\FeeCache;
use App\Services\Transactions\Aggregates\Fees\AverageAggregateFactory;
use App\Services\Transactions\Aggregates\Fees\HistoricalAggregateFactory;
use App\Services\Transactions\Aggregates\Fees\MaximumAggregateFactory;
use App\Services\Transactions\Aggregates\Fees\MinimumAggregateFactory;
use Illuminate\Console\Command;

final class CacheFeeChart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:chart-fee';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(FeeCache $cache)
    {
        foreach (['day', 'week', 'month', 'quarter', 'year'] as $period) {
            $cache->setHistorical($period, HistoricalAggregateFactory::make($period)->aggregate());
            $cache->setMinimum($period, MinimumAggregateFactory::make($period)->aggregate());
            $cache->setAverage($period, AverageAggregateFactory::make($period)->aggregate());
            $cache->setMaximum($period, MaximumAggregateFactory::make($period)->aggregate());
        }
    }
}
