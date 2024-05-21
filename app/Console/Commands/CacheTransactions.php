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
            if (! $this->hasChanges && $cache->getHistorical($period) !== $this->chartjs($value)) {
                $this->hasChanges = true;
            }

            $cache->setHistorical($period, $value);
        });

        StatsTransactionType::all()
            ->each(function ($type) use ($cache) {
                $value = HistoricalAggregateFactory::type($type)->aggregate();
                if (! $this->hasChanges && $cache->getHistoricalByType($type) !== $value) {
                    $this->hasChanges = true;
                }

                $cache->setHistoricalByType($type, $value);
            });

        $averagesValue = HistoricalAggregateFactory::averages()->aggregate();
        if (! $this->hasChanges && $cache->getHistoricalAverages() !== $averagesValue) {
            $this->hasChanges = true;
        }

        $cache->setHistoricalAverages($averagesValue);

        $largestTransaction = (new LargestTransactionAggregate())->aggregate();
        if ($largestTransaction !== null) {
            if (! $this->hasChanges && $cache->getLargestIdByAmount() !== $largestTransaction->id) {
                $this->hasChanges = true;
            }

            $cache->setLargestIdByAmount($largestTransaction->id);
        }

        $this->dispatchEvent(TransactionDetails::class);
    }
}
