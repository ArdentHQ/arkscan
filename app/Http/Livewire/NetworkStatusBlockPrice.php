<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Network;
use App\Facades\Settings;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\NumberFormatter;
use ARKEcosystem\Foundation\NumberFormatter\NumberFormatter as BetterNumberFormatter;
use Illuminate\View\View;
use Livewire\Component;

final class NetworkStatusBlockPrice extends Component
{
    /** @phpstan-ignore-next-line */
    protected $listeners = [
        'currencyChanged' => '$refresh',
    ];

    public function render(): View
    {
        return view('livewire.network-status-block-price', [
            'price'       => $this->getPriceFormatted(),
            'priceChange' => $this->getPriceChange(),
        ]);
    }

    private function getPriceChange(): ?float
    {
        return (new NetworkStatusBlockCache())->getPriceChange(Network::currency(), Settings::currency());
    }

    private function getPriceFormatted(): ? string
    {
        $currency = Settings::currency();
        $price    = (new NetworkStatusBlockCache())->getPrice(Network::currency(), $currency);

        if ($price === null) {
            return null;
        }

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
}
