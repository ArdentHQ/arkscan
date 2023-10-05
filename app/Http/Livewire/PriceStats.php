<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Enums\StatsPeriods;
use App\Facades\Network;
use App\Facades\Settings;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\Cache\PriceChartCache;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

final class PriceStats extends Component
{
    /** @var mixed */
    protected $listeners = ['currencyChanged' => '$refresh'];

    public function render(): View
    {
        $chartData = $this->getHistorical();

        return view('livewire.price-stats', [
            'from'           => Network::currency(),
            'to'             => Settings::currency(),
            'historical'     => $chartData,
            'isPositive'     => $this->isPositivePriceChange($chartData->values()),
            'usePlaceholder' => $this->shouldUsePlaceholder(),
        ]);
    }

    private function isPositivePriceChange(Collection $dataset): bool
    {
        $initialValue = $dataset->first();
        $currentValue = (new NetworkStatusBlockCache())->getPrice(Network::currency(), Settings::currency()) ?? 0.0;

        return $initialValue < $currentValue;
    }

    private function getHistorical(): Collection
    {
        $historicalData = $this->getHistoricalData();

        if (! Network::canBeExchanged() || $historicalData === null) {
            return collect([4, 5, 2, 2, 2, 3, 5, 1, 4, 5, 6, 5, 3, 3, 4, 5, 6, 4, 4, 4, 5, 8, 8, 10]);
        }

        return $historicalData;
    }

    private function getHistoricalData(): ?Collection
    {
        /** @var array<int, float> $historicalData */
        $historicalData = collect((new PriceChartCache())->getHistorical(Settings::currency(), StatsPeriods::DAY))->get('datasets');

        if (count($historicalData) === 0) {
            return null;
        }

        return collect($historicalData);
    }

    private function shouldUsePlaceholder(): bool
    {
        if (! Network::canBeExchanged()) {
            return true;
        }

        if ($this->getHistoricalData() === null) {
            return true;
        }

        return false;
    }
}
