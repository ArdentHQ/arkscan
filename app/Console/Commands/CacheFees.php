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

        collect(Forms::getTransactionOptions())->except('all')->keys()->each(function ($type) use ($cache): void {
            preg_match('/^[a-z]+(\d+)$/', self::LAST_20, $match);

            $result = (new LastFeeAggregate())
                ->setLimit((int) $match[1])
                ->setType($type ?? '')
                ->aggregate();

            $cache->setMinimum($type, $result['minimum']);
            $cache->setAverage($type, $result['average']);
            $cache->setMaximum($type, $result['maximum']);
        });
    }
}
