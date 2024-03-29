<?php

declare(strict_types=1);

namespace App\Http\Livewire\Stats;

use App\Enums\StatsCache;
use App\Enums\StatsPeriods;
use App\Http\Livewire\Concerns\AvailablePeriods;
use App\Http\Livewire\Concerns\ChartNumberFormatters;
use App\Http\Livewire\Concerns\StatisticsChart;
use Illuminate\View\View;
use Livewire\Component;

final class AllTimeTransactions extends Component
{
    use AvailablePeriods;
    use ChartNumberFormatters;
    use StatisticsChart;

    public string $refreshInterval = '';

    public string $cache = '';

    /** @var mixed */
    protected $listeners = ['themeChanged' => '$refresh'];

    public function mount(): void
    {
        $this->refreshInterval = (string) config('arkscan.statistics.refreshInterval', '60');
        $this->period          = $this->defaultPeriod();
        $this->cache           = StatsCache::TRANSACTIONS;
    }

    public function render(): View
    {
        return view('livewire.stats.all-time-transactions', [
            'allTimeTransactionsTitle' => trans('pages.statistics.information-cards.all-time-transactions'),
            'allTimeTransactionsValue' => $this->asNumber($this->totalTransactionsPerPeriod($this->cache, StatsPeriods::ALL)),
            'transactionsTitle'        => trans('pages.statistics.information-cards.transactions'),
            'transactionsValue'        => $this->asNumber($this->totalTransactionsPerPeriod($this->cache, $this->period)),
            'chartValues'              => $this->chartTotalTransactionsPerPeriod($this->cache, $this->period),
            'chartTheme'               => $this->chartTheme('black'),
            'options'                  => $this->availablePeriods(),
            'refreshInterval'          => $this->refreshInterval,
        ]);
    }
}
