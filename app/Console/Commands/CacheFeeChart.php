<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Cache\FeeChartCache;
use App\Services\Transactions\Aggregates\FeesByDayAggregate;
use App\Services\Transactions\Aggregates\FeesByMonthAggregate;
use App\Services\Transactions\Aggregates\FeesByQuarterAggregate;
use App\Services\Transactions\Aggregates\FeesByWeekAggregate;
use App\Services\Transactions\Aggregates\FeesByYearAggregate;
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
    public function handle(FeeChartCache $cache)
    {
        $cache->setDay((new FeesByDayAggregate())->aggregate());

        $cache->setWeek((new FeesByWeekAggregate())->aggregate());

        $cache->setMonth((new FeesByMonthAggregate())->aggregate());

        $cache->setQuarter((new FeesByQuarterAggregate())->aggregate());

        $cache->setYear((new FeesByYearAggregate())->aggregate());
    }
}
