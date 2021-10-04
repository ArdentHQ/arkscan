<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\StatsPeriods;
use App\Services\Cache\TransactionCache;
use App\Services\Transactions\Aggregates\HistoricalAggregateFactory;
use Illuminate\Console\Command;

final class CacheTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache expensive transactions aggregates.';

    public function handle(TransactionCache $cache): void
    {
        collect([
            StatsPeriods::DAY,
            StatsPeriods::WEEK,
            StatsPeriods::MONTH,
            StatsPeriods::QUARTER,
            StatsPeriods::YEAR,
            StatsPeriods::ALL,
        ])->each(fn ($period) => $cache->setHistorical($period, HistoricalAggregateFactory::make($period)->aggregate()));
    }
}
