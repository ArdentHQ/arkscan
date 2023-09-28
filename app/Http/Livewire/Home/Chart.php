<?php

declare(strict_types=1);

namespace App\Http\Livewire\Home;

use App\Enums\CryptoCurrencies;
use App\Enums\StatsPeriods;
use App\Facades\Network;
use App\Facades\Settings;
use App\Http\Livewire\Concerns\AvailablePeriods;
use App\Http\Livewire\Concerns\StatisticsChart;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\MarketCap;
use App\Services\NumberFormatter as ServiceNumberFormatter;
use ARKEcosystem\Foundation\NumberFormatter\NumberFormatter as BetterNumberFormatter;
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
        return view('livewire.home.chart', [
            'mainValueFiat'       => $this->mainValueFiat(),
            'chart'               => $this->chartHistoricalPrice($this->period),
            'chartTheme'          => $this->chartTheme($this->mainValueVariation() === 'up' ? 'green' : 'red'),
            'options'             => $this->availablePeriods(),
            'refreshInterval'     => $this->refreshInterval,
        ]);
    }

    public function setPeriod(string $period): void
    {
        if (! in_array($period, array_keys(trans('pages.home.charts.periods')), true)) {
            return;
        }

        $this->period = $period;
    }

    private function mainValueFiat(): string
    {
        $currency = Settings::currency();
        $price    = $this->getPrice($currency);

        if (ServiceNumberFormatter::isFiat($currency)) {
            return BetterNumberFormatter::new()
                ->withLocale(Settings::locale())
                ->withFractionDigits(2)
                ->formatWithCurrencyAccounting($price);
        }

        return BetterNumberFormatter::new()
                ->formatWithCurrencyCustom(
                    $price,
                    $currency,
                    ServiceNumberFormatter::CRYPTO_DECIMALS
                );
    }

    private function getPrice(string $currency): float
    {
        return (new NetworkStatusBlockCache())->getPrice(Network::currency(), $currency) ?? 0.0;
    }

    private function mainValueVariation(): string
    {
        return $this->getPriceChange() < 0 ? 'down' : 'up';
    }

    private function getPriceChange(): ?float
    {
        return (new NetworkStatusBlockCache())->getPriceChange(Network::currency(), Settings::currency());
    }

    private function getHistoricalHourly(string $target): Collection
    {
        /** @var Collection<int, mixed> */
        return (new NetworkStatusBlockCache())->getHistoricalHourly(Network::currency(), $target) ?? collect([]);
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
