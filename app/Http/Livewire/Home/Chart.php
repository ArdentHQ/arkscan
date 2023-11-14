<?php

declare(strict_types=1);

namespace App\Http\Livewire\Home;

use App\Enums\StatsPeriods;
use App\Facades\Network;
use App\Facades\Settings;
use App\Http\Livewire\Concerns\AvailablePeriods;
use App\Http\Livewire\Concerns\StatisticsChart;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\NumberFormatter as ServiceNumberFormatter;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

final class Chart extends Component
{
    use AvailablePeriods;
    use StatisticsChart;

    public bool $show = true;

    public string $period = '';

    public string $refreshInterval = '';

    /** @var mixed */
    protected $listeners = [
        'currencyChanged' => '$refresh',
        'themeChanged'    => '$refresh',
        'updateChart'     => '$refresh',
    ];

    public function mount(): void
    {
        $this->refreshInterval = (string) config('arkscan.statistics.refreshInterval', '60');
        $this->period          = $this->defaultPeriod();
        $this->show            = Network::canBeExchanged();
    }

    public function render(): View
    {
        $currency = Settings::currency();

        $currentPrice = $this->getPrice($currency);
        $chartData    = $this->chartHistoricalPrice($this->period);

        $datasets = new Collection($chartData->get('datasets'));
        $datasets->pop();
        $datasets->push($currentPrice);

        /** @var array<int> $labels */
        $labels = $chartData->get('labels');

        return view('livewire.home.chart', [
            'mainValueFiat'       => ServiceNumberFormatter::currency($currentPrice, $currency),
            'datasets'            => collect($datasets),
            'labels'              => collect($labels),
            'chartTheme'          => $this->chartTheme($this->mainValueVariation($chartData->get('datasets', []))),
            'options'             => $this->availablePeriods(),
            'refreshInterval'     => $this->refreshInterval,
        ]);
    }

    public function setPeriod(string $period): void
    {
        if (! in_array($period, array_keys($this->availablePeriods()), true)) {
            return;
        }

        $this->period = $period;
    }

    private function getPrice(string $currency): float
    {
        return (new NetworkStatusBlockCache())->getPrice(Network::currency(), $currency) ?? 0.0;
    }

    private function mainValueVariation(array $dataset): string
    {
        // Determine difference based on first datapoint
        $initialValue = collect($dataset)->first();
        $currentValue = $this->getPrice(Settings::currency());

        return $initialValue > $currentValue ? 'red' : 'green';
    }

    private function availablePeriods(): array
    {
        return [
            StatsPeriods::ALL   => trans('pages.home.charts.periods.all'),
            StatsPeriods::DAY   => trans('pages.home.charts.periods.day'),
            StatsPeriods::WEEK  => trans('pages.home.charts.periods.week'),
            StatsPeriods::MONTH => trans('pages.home.charts.periods.month'),
            StatsPeriods::YEAR  => trans('pages.home.charts.periods.year'),
        ];
    }
}
