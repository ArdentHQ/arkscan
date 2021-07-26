<?php

declare(strict_types=1);

namespace App\Http\Livewire\Stats;

use App\Enums\StatsPeriods;
use App\Http\Livewire\Concerns\AvailablePeriods;
use App\Http\Livewire\Concerns\ChartNumberFormatters;
use App\Http\Livewire\Concerns\StatisticsChart;
use App\Services\Cache\TransactionCache;
use Illuminate\View\View;
use Livewire\Component;

final class InsightAllTimeTransactions extends Component
{
    use AvailablePeriods;
    use ChartNumberFormatters;
    use StatisticsChart;

    public string $period = '';

    public string $refreshInterval = '';

    /** @phpstan-ignore-next-line */
    protected $listeners = ['toggleDarkMode' => '$refresh'];

    public function mount(): void
    {
        $this->refreshInterval = (string) config('explorer.statistics.refreshInterval', '60');
        $this->period          = $this->defaultPeriod();
    }

    public function render(): View
    {
        return view('livewire.stats.insight-all-time-transactions', [
            'allTimeTransactionsTitle' => trans('pages.statistics.insights.all-time-transactions'),
            'allTimeTransactionsValue' => $this->asNumber($this->totalTransactionsPerPeriod(TransactionCache::class, StatsPeriods::ALL)),
            'transactionsTitle'        => trans('pages.statistics.insights.transactions'),
            'transactionsValue'        => $this->asNumber($this->totalTransactionsPerPeriod(TransactionCache::class, $this->period)),
            'chartValues'              => $this->chartTotalTransactionsPerPeriod(TransactionCache::class, $this->period),
            'chartTheme'               => $this->chartTheme('black'),
            'options'                  => $this->availablePeriods(),
            'refreshInterval'          => $this->refreshInterval,
        ]);
    }
}
