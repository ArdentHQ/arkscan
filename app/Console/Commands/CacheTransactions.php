<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\StatsPeriods;
use App\Enums\StatsTransactionType;
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
     * @var string|null
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
        ])->each(fn ($period) => $cache->setHistorical($period, HistoricalAggregateFactory::period($period)->aggregate()));

        StatsTransactionType::all()
            ->each(fn ($type) => $cache->setHistoricalByType($type, HistoricalAggregateFactory::type($type)->aggregate()));

        $cache->setHistoricalAverages(HistoricalAggregateFactory::averages()->aggregate());
    }
}
