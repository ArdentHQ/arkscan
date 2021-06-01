<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Network;
use App\Services\CryptoCompare;
use App\Services\Settings;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

final class PriceStats extends Component
{
    /** @phpstan-ignore-next-line */
    protected $listeners = ['currencyChanged' => '$refresh'];

    public function render(): View
    {
        return view('livewire.price-stats', [
            'from'        => Network::currency(),
            'to'          => Settings::currency(),
            'historical'  => $this->getHistorical(),
        ]);
    }

    private function getHistorical(): Collection
    {
        if (! Network::canBeExchanged()) {
            return collect([4, 5, 2, 2, 2, 3, 5, 1, 4, 5, 6, 5, 3, 3, 4, 5, 6, 4, 4, 4, 5, 8, 8, 10]);
        }

        return CryptoCompare::historicalHourly(Network::currency(), Settings::currency());
    }
}
