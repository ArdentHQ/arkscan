<?php

declare(strict_types=1);

namespace App\Http\Livewire\Exchanges;

use App\Enums\CryptoCurrencies;
use App\Enums\StatsPeriods;
use App\Facades\Network;
use App\Facades\Settings;
use App\Http\Livewire\Concerns\HandlesChart;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\MarketCap;
use App\Services\NumberFormatter as ServiceNumberFormatter;
use ARKEcosystem\Foundation\NumberFormatter\NumberFormatter as BetterNumberFormatter;
use Illuminate\View\View;
use Livewire\Component;

final class Chart extends Component
{
    use HandlesChart;

    public function render(): View | string
    {
        if (! Network::canBeExchanged()) {
            return '<div></div>';
        }

        $chartData = $this->chartHistoricalPrice($this->period);

        /** @var array<float> $datasets */
        $datasets = $chartData->get('datasets', []);

        /** @var array<int> $labels */
        $labels = $chartData->get('labels', []);

        $variation = $this->mainValueVariation($datasets);

        return view('livewire.exchanges.chart', [
            'mainValue'           => $this->mainValueBTC(),
            'mainValueFiat'       => $this->mainValueFiat(),
            'mainValuePercentage' => $this->mainValuePercentage($datasets),
            'mainValueVariation'  => $variation,
            'marketCapValue'      => $this->marketCap(),
            'minPriceValue'       => $this->minPrice($datasets),
            'maxPriceValue'       => $this->maxPrice($datasets),
            'datasets'            => collect($datasets),
            'labels'              => collect($labels),
            'chartTheme'          => $this->chartTheme($variation),
            'options'             => $this->availablePeriods(),
            'refreshInterval'     => $this->refreshInterval,
        ]);
    }

    public function updatedPeriod(): void
    {
        $this->dispatch('stats-period-updated', []);
    }

    public function getDateUnitProperty(): ?string
    {
        if ($this->period === StatsPeriods::WEEK) {
            return 'day';
        }

        return null;
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

    private function mainValuePercentage(array $dataset): float
    {
        // Determine difference based on first datapoint
        $initialValue = collect($dataset)->first();
        $currentValue = $this->getPrice(Settings::currency());

        if ($currentValue === 0.0) {
            return 0;
        }

        return (1 - ($initialValue / $currentValue)) * 100;
    }

    private function marketCap(): ?string
    {
        return MarketCap::getFormatted(Network::currency(), Settings::currency());
    }

    private function getPrice(string $currency): float
    {
        return (new NetworkStatusBlockCache())->getPrice(Network::currency(), $currency) ?? 0.0;
    }

    private function minPrice(array $dataset): string
    {
        return ServiceNumberFormatter::currency((float) collect($dataset)->min(), Settings::currency());
    }

    private function maxPrice(array $dataset): string
    {
        return ServiceNumberFormatter::currency((float) collect($dataset)->max(), Settings::currency());
    }
}
