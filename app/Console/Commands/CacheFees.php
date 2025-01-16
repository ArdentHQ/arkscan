<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\StatsPeriods;
use App\Services\Cache\FeeCache;
use App\Services\Forms;
use App\Services\Transactions\Aggregates\Fees\HistoricalAggregateFactory;
use App\Services\Transactions\Aggregates\Fees\LastFeeAggregate;
use Illuminate\Console\Command;

final class CacheFees extends Command
{
    private const LIMIT_COUNT = 20;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-fees';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Cache expensive fee aggregates.';

    public function handle(FeeCache $cache): void
    {
        $cache->setHistorical(StatsPeriods::ALL, HistoricalAggregateFactory::make(StatsPeriods::ALL)->aggregate());

        collect([
            StatsPeriods::DAY,
            StatsPeriods::WEEK,
            StatsPeriods::MONTH,
            StatsPeriods::QUARTER,
            StatsPeriods::YEAR,
        ])->each(function ($period) use ($cache): void {
            $cache->setHistorical($period, HistoricalAggregateFactory::make($period)->aggregate());
        });

        collect(Forms::getTransactionOptions())->except(['all'])->keys()->each(function (string $type) use ($cache): void {
            $result = (new LastFeeAggregate())
                ->setLimit(self::LIMIT_COUNT)
                ->setType($type)
                ->aggregate();

            $cache->setMinimum($type, $result['minimum']);
            $cache->setAverage($type, $result['average']);
            $cache->setMaximum($type, $result['maximum']);
        });
    }
}
