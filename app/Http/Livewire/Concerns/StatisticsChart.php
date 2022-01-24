<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Facades\Settings;
use App\Services\Cache\FeeCache;
use App\Services\Cache\PriceChartCache;
use App\Services\Cache\TransactionCache;
use Illuminate\Support\Collection;
use InvalidArgumentException;

trait StatisticsChart
{
    private function chartTheme(string $color): Collection
    {
        return collect(['name' => $color, 'mode' => Settings::theme()]);
    }

    private function chartHistoricalPrice(string $period): Collection
    {
        return collect((new PriceChartCache())->getHistorical(Settings::currency(), $period));
    }

    private function chartTotalTransactionsPerPeriod(string $cache, string $period): Collection
    {
        return $this->transactionsPerPeriod($cache, $period);
    }

    private function totalTransactionsPerPeriod(string $cache, string $period): int | float
    {
        $datasets = $this->transactionsPerPeriod($cache, $period)->get('datasets');

        return collect($datasets)->sum();
    }

    private function transactionsPerPeriod(string $cache, string $period): Collection
    {
        if ($cache === FeeCache::class) {
            return collect((new FeeCache())->getHistorical($period));
        }

        if ($cache === TransactionCache::class) {
            return collect((new TransactionCache())->getHistorical($period));
        }

        throw new InvalidArgumentException("Given cache [$cache] is invalid. Use FeeCache or TransactionCache.");
    }
}
