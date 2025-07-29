<?php

declare(strict_types=1);

namespace App\Http\Livewire\Stats;

use App\Enums\StatsCache;
use App\Enums\StatsPeriods;
use App\Facades\Network;
use App\Http\Livewire\Concerns\AvailablePeriods;
use App\Http\Livewire\Concerns\ChartNumberFormatters;
use App\Http\Livewire\Concerns\StatisticsChart;
use App\Services\NumberFormatter;
use Brick\Math\BigDecimal;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * @property int|float $periodTotal
 * @property bool $isAboveThreshold
 */
final class AllTimeFees extends Component
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
        $this->cache           = StatsCache::FEES;
    }

    public function render(): View
    {
        return view('livewire.stats.all-time-fees', [
            'allTimeFeesCollectedTitle' => trans('pages.statistics.information-cards.all-time-fees-collected'),
            'allTimeFeesCollectedValue' => $this->allTimeFees(),
            'feesTitle'                 => trans('pages.statistics.information-cards.fees'),
            'feesValue'                 => $this->truncate(),
            'feesTooltip'               => $this->tooltip(),
            'chartValues'               => $this->chartValues(),
            'chartTheme'                => $this->chartTheme('yellow'),
            'options'                   => $this->availablePeriods(),
            'refreshInterval'           => $this->refreshInterval,
        ]);
    }

    #[Computed()]
    protected function periodTotal(): int | float
    {
        return $this->totalTransactionsPerPeriod($this->cache, $this->period);
    }

    #[Computed()]
    protected function isAboveThreshold(): bool
    {
        return $this->periodTotal > 10000 * 1e18;
    }

    private function tooltip(): ?string
    {
        return $this->isAboveThreshold ? $this->asMoney($this->periodTotal) : null;
    }

    private function truncate(): string
    {
        if ($this->isAboveThreshold) {
            $convertedAmount = NumberFormatter::weiToArk((string) BigDecimal::of($this->periodTotal), false);

            return sprintf('%s %s', $this->asNumber($convertedAmount), Network::currency());
        }

        return $this->asMoney($this->periodTotal);
    }

    private function allTimeFees(): string
    {
        return $this->asMoney($this->totalTransactionsPerPeriod($this->cache, StatsPeriods::ALL));
    }

    private function chartValues(): Collection
    {
        $values = $this->chartTotalTransactionsPerPeriod($this->cache, $this->period);

        $datasets = (new Collection($values->get('datasets')))
            ->map(function ($value) {
                return BigDecimal::of(NumberFormatter::weiToArk((string) BigDecimal::of($value), false))->toFloat();
            });

        $values->put('datasets', $datasets);

        return $values;
    }

    private function asMoney(string | int | float $value): string
    {
        return NumberFormatter::currency(
            NumberFormatter::weiToArk((string) BigDecimal::of($value)),
            Network::currency(),
        );
    }
}
