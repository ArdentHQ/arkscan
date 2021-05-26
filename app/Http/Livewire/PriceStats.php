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
    public bool $placeholder = false;

    public function mount(bool $placeholder = false) : void
    {
        $this->placeholder = $placeholder;
    }

    public function render(): View
    {
        return view('livewire.price-stats', [
            'from'        => Network::currency(),
            'to'          => Settings::currency(),
            'priceChange' => $this->getPriceChange(),
            'historical'  => $this->getHistorical(),
        ]);
    }

    private function getPriceChange(): ?float
    {
        if ($this->placeholder) {
            return null;
        }

        $priceFullRange = CryptoCompare::historicalHourly(Network::currency(), Settings::currency(), 24);

        $initialPrice = (float) $priceFullRange->first();
        $finalPrice   = (float) $priceFullRange->last();

        if ($initialPrice === 0.0 || $finalPrice === 0.0) {
            return  0;
        }

        return ($finalPrice / $initialPrice) - 1;
    }

    private function getHistorical(): Collection
    {
        if ($this->placeholder) {
            return collect([4, 5, 2, 2, 2, 3, 5, 1, 4, 5, 6, 5, 3, 3, 4, 5, 6, 4, 4, 4, 5, 8, 8, 10]);
        }

        return CryptoCompare::historicalHourly(Network::currency(), Settings::currency());
    }
}
