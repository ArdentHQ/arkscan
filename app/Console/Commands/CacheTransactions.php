<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Concerns\DispatchesStatisticsEvents;
use App\Enums\StatsPeriods;
use App\Enums\StatsTransactionType;
use App\Events\Statistics\TransactionDetails;
use App\Services\Cache\Concerns\ManagesChart;
use App\Services\Cache\TransactionCache;
use App\Services\Transactions\Aggregates\HistoricalAggregateFactory;
use App\Services\Transactions\Aggregates\LargestTransactionAggregate;
use Illuminate\Console\Command;

final class CacheTransactions extends Command
{
    use DispatchesStatisticsEvents;
    use ManagesChart;

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
        ])->each(function ($period) use ($cache) {
            $value = HistoricalAggregateFactory::period($period)->aggregate();
            // This covers all changes to transactions
            if (! $this->hasChanges && $cache->getHistorical($period) !== $this->chartjs($value)) {
                $this->hasChanges = true;
            }

            $cache->setHistorical($period, $value);
        });

        StatsTransactionType::all()
            ->each(function ($type) use ($cache) {
                $cache->setHistoricalByType($type, HistoricalAggregateFactory::type($type)->aggregate());
            });

        $largestTransaction = (new LargestTransactionAggregate())->aggregate();
        if ($largestTransaction !== null) {
            $cache->setLargestIdByAmount($largestTransaction->id);
        }

        $averagesValue = HistoricalAggregateFactory::averages()->aggregate();

        $cache->setHistoricalAverages($averagesValue);

        $this->dispatchEvent(TransactionDetails::class);
    }
}
