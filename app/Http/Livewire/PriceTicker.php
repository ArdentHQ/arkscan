<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Network;
use App\Services\CryptoCompare;
use App\Services\Settings;
use Illuminate\View\View;
use Livewire\Component;

final class PriceTicker extends Component
{
    /** @phpstan-ignore-next-line */
    protected $listeners = ['currencyChanged' => 'setValues'];

    public string $price;

    public string $from;

    public string $to;

    public function mount(): void
    {
        $this->setValues();
    }

    public function setValues(): void
    {
        $this->price = number_format(CryptoCompare::price(Network::currency(), Settings::currency()), 2);
        $this->from  = Network::currency();
        $this->to    = Settings::currency();
    }

    public function render(): View
    {
        return view('livewire.price-ticker', [
            'from'  => $this->from,
            'to'    => $this->to,
            'price' => $this->price,
        ]);
    }
}
