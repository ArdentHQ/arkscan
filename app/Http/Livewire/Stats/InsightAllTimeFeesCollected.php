<?php

declare(strict_types=1);

namespace App\Http\Livewire\Stats;

use App\Enums\StatsCache;
use App\Enums\StatsPeriods;
use App\Facades\Network;
use App\Http\Livewire\Concerns\AvailablePeriods;
use App\Http\Livewire\Concerns\ChartNumberFormatters;
use App\Http\Livewire\Concerns\StatisticsChart;
use Illuminate\View\View;
use Livewire\Component;

final class InsightAllTimeFeesCollected extends Component
{
    use AvailablePeriods;
    use ChartNumberFormatters;
    use StatisticsChart;

    public string $period = '';

    public string $refreshInterval = '';

    public string $cache = '';

    /** @var mixed */
    protected $listeners = ['themeChanged' => '$refresh'];

    public function mount(): void
    {
        $this->refreshInterval = (string) config('explorer.statistics.refreshInterval', '60');
        $this->period          = $this->defaultPeriod();
        $this->cache           = StatsCache::FEES;
    }

    public function render(): View
    {
        return view('livewire.stats.insight-all-time-fees-collected', [
            'allTimeFeesCollectedTitle' => trans('pages.statistics.insights.all-time-fees-collected'),
            'allTimeFeesCollectedValue' => $this->asMoney($this->totalTransactionsPerPeriod($this->cache, StatsPeriods::ALL)),
            'feesTitle'                 => trans('pages.statistics.insights.fees'),
            'feesValue'                 => $this->truncate(),
            'feesTooltip'               => $this->tooltip(),
            'chartValues'               => $this->chartTotalTransactionsPerPeriod($this->cache, $this->period),
            'chartTheme'                => $this->chartTheme('yellow'),
            'options'                   => $this->availablePeriods(),
            'refreshInterval'           => $this->refreshInterval,
        ]);
    }

    private function tooltip(): ?string
    {
        $number = $this->totalTransactionsPerPeriod($this->cache, $this->period);

        return $number < 10000 ? null : $this->asMoney($number);
    }

    private function truncate(): string
    {
        $number = $this->totalTransactionsPerPeriod($this->cache, $this->period);

        return $number > 10000
            ? sprintf('%s %s', $this->asNumber($number), Network::currency())
            : $this->asMoney($number);
    }
}
