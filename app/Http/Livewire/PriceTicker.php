<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Network;
use App\Facades\Settings;
use App\Http\Livewire\Concerns\HandlesSettings;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\NumberFormatter;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;

final class PriceTicker extends Component
{
    use HandlesSettings;

    public string $price;

    public string $to;

    public bool $isAvailable = false;

    /** @var mixed */
    protected $listeners = [
        'currencyChanged' => 'setValues',
    ];

    public function mount(): void
    {
        $this->setValues();
    }

    public function setValues(): void
    {
        $this->isAvailable = (new NetworkStatusBlockCache())->getIsAvailable(Network::currency(), Settings::currency());
        $this->price       = $this->getPriceFormatted();
        $this->to          = Settings::currency();

        $this->dispatchBrowserEvent('has-loaded-price-data');
    }

    public function render(): View
    {
        return view('livewire.price-ticker', [
            'to'    => $this->to,
            'price' => $this->price,
        ]);
    }

    public function setCurrency(string $newCurrency): void
    {
        $originalCurrency = Settings::currency();
        $newCurrency = Str::upper($newCurrency);

        if ($originalCurrency !== $newCurrency) {
            $this->saveSetting('currency', $newCurrency);

            $this->emit('currencyChanged', $newCurrency);
        }
    }

    private function getPriceFormatted(): string
    {
        $price = (new NetworkStatusBlockCache())->getPrice(Network::currency(), Settings::currency());

        if ($price === null) {
            return '';
        }

        return NumberFormatter::currencyWithDecimalsWithoutSuffix($price, Settings::currency());
    }
}
