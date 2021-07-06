<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Services\Settings;
use Illuminate\Support\Collection;

trait StatisticsChart
{
    private function chartTheme(string $color): Collection
    {
        return collect([
            'name' => $color,
            'mode' => Settings::theme(),
        ]);
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
        return collect((new $cache())->getHistorical($period));
    }
}
