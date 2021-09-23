<?php

declare(strict_types=1);

namespace App\Http\Livewire\Stats;

use App\Enums\CryptoCurrencies;
use App\Facades\Network;
use App\Http\Livewire\Concerns\AvailablePeriods;
use App\Http\Livewire\Concerns\StatisticsChart;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\MarketCap;
use App\Services\NumberFormatter as ServiceNumberFormatter;
use App\Services\Settings;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Konceiver\BetterNumberFormatter\BetterNumberFormatter;
use Livewire\Component;

final class Chart extends Component
{
    use AvailablePeriods;
    use StatisticsChart;

    public bool $show = true;

    public string $period = '';

    public string $refreshInterval = '';

    /** @phpstan-ignore-next-line */
    protected $listeners = [
        'currencyChanged' => '$refresh',
        'toggleDarkMode'  => '$refresh',
        'updateChart'     => '$refresh',
    ];

    public function mount(): void
    {
        $this->refreshInterval = (string) config('explorer.statistics.refreshInterval', '60');
        $this->period          = $this->defaultPeriod();
        $this->show            = Network::canBeExchanged();
    }

    public function render(): View
    {
        return view('livewire.stats.chart', [
            'mainValue'           => $this->mainValueBTC(),
            'mainValueFiat'       => $this->mainValueFiat(),
            'mainValuePercentage' => $this->mainValuePercentage(),
            'mainValueVariation'  => $this->mainValueVariation(),
            'marketCapValue'      => $this->marketCap(),
            'minPriceValue'       => $this->minPrice(),
            'maxPriceValue'       => $this->maxPrice(),
            'chart'               => $this->chartHistoricalPrice($this->period),
            'chartTheme'          => $this->chartTheme($this->mainValueVariation() === 'up' ? 'green' : 'red'),
            'options'             => $this->availablePeriods(),
            'refreshInterval'     => $this->refreshInterval,
        ]);
    }

    public function updatedPeriod(): void
    {
        $this->dispatchBrowserEvent('stats-period-updated', []);
    }

    private function mainValueBTC(): string
    {
        return ServiceNumberFormatter::currency($this->getPrice(CryptoCurrencies::BTC), CryptoCurrencies::BTC);
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

    private function mainValuePercentage(): float
    {
        return abs((float) $this->getPriceChange()) * 100;
    }

    private function mainValueVariation(): string
    {
        return $this->getPriceChange() < 0 ? 'down' : 'up';
    }

    private function marketCap(): string
    {
        return MarketCap::getFormatted(Network::currency(), Settings::currency()) ?? '0';
    }

    private function getPriceChange(): ?float
    {
        return (new NetworkStatusBlockCache())->getPriceChange(Network::currency(), Settings::currency());
    }

    private function getPrice(string $currency): float
    {
        return (new NetworkStatusBlockCache())->getPrice(Network::currency(), $currency) ?? 0.0;
    }

    private function getPriceRange(): Collection
    {
        return $this->getHistoricalHourly(CryptoCurrencies::BTC);
    }

    private function minPrice(): string
    {
        $range = $this->getPriceRange();

        return ServiceNumberFormatter::currency((float) $range->min(), CryptoCurrencies::BTC);
    }

    private function maxPrice(): string
    {
        $range = $this->getPriceRange();

        return ServiceNumberFormatter::currency((float) $range->max(), CryptoCurrencies::BTC);
    }

    private function getHistoricalHourly(string $target): Collection
    {
        return (new NetworkStatusBlockCache())->getHistoricalHourly(Network::currency(), $target) ?? collect();
    }
}
