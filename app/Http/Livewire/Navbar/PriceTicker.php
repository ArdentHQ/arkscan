<?php

declare(strict_types=1);

namespace App\Http\Livewire\Navbar;

use App\Facades\Network;
use App\Facades\Settings;
use App\Http\Livewire\Concerns\HandlesSettings;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\ExchangeRate;
use App\Services\NumberFormatter;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;

final class PriceTicker extends Component
{
    use HandlesSettings;

    public string $to;

    public bool $isAvailable = false;

    /** @var mixed */
    protected $listeners = [
        'currencyChanged'   => 'setValues',
        'reloadPriceTicker' => 'setValues',
    ];

    public function mount(): void
    {
        $this->setValues();
    }

    public function setValues(): void
    {
        $cache = new NetworkStatusBlockCache();
        foreach (config('currencies') as $currency) {
            if (! $cache->getIsAvailable(Network::currency(), $currency['currency'])) {
                continue;
            }

            $this->isAvailable = true;
        }

        $this->to = Settings::currency();

        $this->dispatch('has-loaded-price-data');
    }

    public function render(): View
    {
        return view('livewire.navbar.price-ticker', [
            'to'    => $this->to,
            'price' => $this->getPriceFormatted(),
        ]);
    }

    public function setCurrency(string $newCurrency): void
    {
        $originalCurrency = Settings::currency();
        $newCurrency      = Str::upper($newCurrency);

        if ($originalCurrency !== $newCurrency) {
            $this->saveSetting('currency', $newCurrency);

            $this->dispatch('currencyChanged', $newCurrency);
        }
    }

    private function getPriceFormatted(): string
    {
        $price = ExchangeRate::currentRate();
        if ($price === null) {
            return '';
        }

        return NumberFormatter::currencyWithDecimalsWithoutSuffix($price, Settings::currency());
    }
}
