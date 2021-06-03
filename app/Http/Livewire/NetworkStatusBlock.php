<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Actions\CacheNetworkHeight;
use App\Actions\CacheNetworkSupply;
use App\Facades\Network;
use App\Services\CryptoCompare;
use App\Services\NumberFormatter;
use App\Services\Settings;
use Illuminate\View\View;
use Konceiver\BetterNumberFormatter\BetterNumberFormatter;
use Livewire\Component;

final class NetworkStatusBlock extends Component
{
    /** @phpstan-ignore-next-line */
    protected $listeners = ['refreshNetworkStatusBlock' => '$refresh'];

    public function render(): View
    {
        return view('livewire.network-status-block', [
            'price'       => $this->getPriceFormatted(),
            'priceChange' => CryptoCompare::getPriceChange(),
            'height'      => CacheNetworkHeight::execute(),
            'network'     => Network::name(),
            'supply'      => CacheNetworkSupply::execute() / 1e8,
            'marketCap'   => $this->getMarketCapFormatted(),
        ]);
    }

    private function getPriceFormatted(): string
    {
        $currency = Settings::currency();
        $price    = CryptoCompare::price(Network::currency(), $currency);

        if (NumberFormatter::isFiat($currency)) {
            return BetterNumberFormatter::new()
                ->withLocale(Settings::locale())
                ->formatWithCurrencyAccounting($price);
        }

        return BetterNumberFormatter::new()
            ->formatWithCurrencyCustom(
                $price,
                $currency,
                NumberFormatter::CRYPTO_DECIMALS
            );
    }

    private function getMarketCapFormatted(): string
    {
        $currency = Settings::currency();
        $price    = $this->getMarketCap();

        if (NumberFormatter::isFiat($currency)) {
            return BetterNumberFormatter::new()
                ->withLocale(Settings::locale())
                ->formatWithCurrencyAccounting($price);
        }

        return BetterNumberFormatter::new()
            ->formatWithCurrencyCustom(
                $price,
                $currency,
                NumberFormatter::CRYPTO_DECIMALS
            );
    }

    private function getMarketCap(): float
    {
        if (! Network::canBeExchanged()) {
            return 0;
        }

        return CryptoCompare::marketCap(Network::currency(), Settings::currency());
    }
}
