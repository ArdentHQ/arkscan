<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\StatsPeriods;
use App\Services\Cache\FeeCache;
use App\Services\Forms;
use App\Services\Transactions\Aggregates\Fees\AverageAggregateFactory;
use App\Services\Transactions\Aggregates\Fees\HistoricalAggregateFactory;
use App\Services\Transactions\Aggregates\Fees\MaximumAggregateFactory;
use App\Services\Transactions\Aggregates\Fees\MinimumAggregateFactory;
use Illuminate\Console\Command;

final class CacheFees extends Command
{
    private const LAST_20 = 'last20';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-fees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache expensive fee aggregates.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(FeeCache $cache)
    {
        $cache->setHistorical(StatsPeriods::ALL, HistoricalAggregateFactory::make(StatsPeriods::ALL)->aggregate());

        collect([
            StatsPeriods::DAY,
            StatsPeriods::WEEK,
            StatsPeriods::MONTH,
            StatsPeriods::QUARTER,
            StatsPeriods::YEAR,
        ])->each(function ($period) use ($cache): void {
            $cache->setMinimum($period, MinimumAggregateFactory::make($period)->aggregate());
            $cache->setAverage($period, AverageAggregateFactory::make($period)->aggregate());
            $cache->setMaximum($period, MaximumAggregateFactory::make($period)->aggregate());
            $cache->setHistorical($period, HistoricalAggregateFactory::make($period)->aggregate());
        });

        collect(Forms::getTransactionOptions())->except('all')->keys()->each(function ($type) use ($cache): void {
            $cache->setMinimum($type, MinimumAggregateFactory::make(self::LAST_20, $type)->aggregate());
            $cache->setAverage($type, AverageAggregateFactory::make(self::LAST_20, $type)->aggregate());
            $cache->setMaximum($type, MaximumAggregateFactory::make(self::LAST_20, $type)->aggregate());
        });
    }
}
