<?php

declare(strict_types=1);

namespace App\Http\Livewire\Home;

use App\Facades\Settings;
use App\Http\Livewire\Concerns\GetsCurrentPrice;
use App\Services\NumberFormatter as ServiceNumberFormatter;
use Illuminate\View\View;
use Livewire\Component;

final class PriceTicker extends Component
{
    use GetsCurrentPrice;

    /** @var mixed */
    protected $listeners = [
        'currencyChanged'   => '$refresh',
        'reloadPriceTicker' => '$refresh',
    ];

    public function render(): View
    {
        $currency     = Settings::currency();
        $currentPrice = $this->getPrice($currency);

        return view('livewire.home.price-ticker', [
            'price' => ServiceNumberFormatter::currency($currentPrice, $currency),
        ]);
    }
}
