<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Network;
use App\Facades\Settings;
use App\Services\Cache\NetworkStatusBlockCache;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

final class PriceStats extends Component
{
    /** @var mixed */
    protected $listeners = ['currencyChanged' => '$refresh'];

    public function render(): View
    {
        return view('livewire.price-stats', [
            'from'           => Network::currency(),
            'to'             => Settings::currency(),
            'historical'     => $this->getHistorical(),
            'isPositive'     => $this->getPriceChange() >= 0,
            'usePlaceholder' => $this->shouldUsePlaceholder(),
        ]);
    }

    private function isAvailable(): bool
    {
        return Network::canBeExchanged()
            && (new NetworkStatusBlockCache())->getIsAvailable(Network::currency(), Settings::currency());
    }

    private function getPriceChange(): ?float
    {
        return (new NetworkStatusBlockCache())->getPriceChange(Network::currency(), Settings::currency());
    }

    private function getHistorical(): Collection
    {
        $historicalData = $this->getHistoricalData();

        if (! $this->isAvailable() || $historicalData === null) {
            return collect([4, 5, 2, 2, 2, 3, 5, 1, 4, 5, 6, 5, 3, 3, 4, 5, 6, 4, 4, 4, 5, 8, 8, 10]);
        }

        return $historicalData;
    }

    private function getHistoricalData(): ? Collection
    {
        return (new NetworkStatusBlockCache())->getHistoricalHourly(Network::currency(), Settings::currency());
    }

    private function shouldUsePlaceholder(): bool
    {
        if (! $this->isAvailable()) {
            return true;
        }

        if ($this->getHistoricalData() === null) {
            return true;
        }

        return false;
    }
}
