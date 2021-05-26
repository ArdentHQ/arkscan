<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Actions\CacheNetworkHeight;
use App\Actions\CacheNetworkSupply;
use App\Facades\Network;
use App\Services\CryptoCompare;
use App\Services\Settings;
use Illuminate\View\View;
use Konceiver\BetterNumberFormatter\BetterNumberFormatter;
use Livewire\Component;
use NumberFormatter;

final class NetworkStatusBlock extends Component
{
    public function render(): View
    {
        return view('livewire.network-status-block', [
            'height'    => CacheNetworkHeight::execute(),
            'network'   => Network::name(),
            'supply'    => CacheNetworkSupply::execute() / 1e8,
            'price'     => $this->getPriceFormatted(),
            'marketCap' => $this->getMarketCapFormatted(),
        ]);
    }

    private function getPriceFormatted(): string
    {
        $price = CryptoCompare::price(Network::currency(), Settings::currency());

        return BetterNumberFormatter::new()->withStyle(NumberFormatter::CURRENCY)->formatWithCurrency($price);
    }

    private function getMarketCapFormatted(): string
    {
        $price = $this->getMarketCap();

        return BetterNumberFormatter::new()->withStyle(NumberFormatter::CURRENCY)->formatWithCurrency($price);
    }

    private function getMarketCap(): float
    {
        if (! Network::canBeExchanged()) {
            return 0;
        }

        return CryptoCompare::marketCap(Network::currency(), Settings::currency());
    }
}
