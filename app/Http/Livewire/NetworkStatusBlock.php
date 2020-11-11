<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Actions\CacheNetworkHeight;
use App\Actions\CacheNetworkSupply;
use App\Facades\Network;
use App\Services\CryptoCompare;
use App\Services\Settings;
use Illuminate\View\View;
use Livewire\Component;

final class NetworkStatusBlock extends Component
{
    public function render(): View
    {
        return view('livewire.network-status-block', [
            'height'    => CacheNetworkHeight::execute(),
            'network'   => Network::name(),
            'supply'    => CacheNetworkSupply::execute() / 1e8,
            'marketCap' => $this->getMarketCap(),
        ]);
    }

    private function getMarketCap(): float
    {
        $marketCap = 0;

        // @codeCoverageIgnoreStart
        if (Network::canBeExchanged()) {
            $marketCap = CacheNetworkSupply::execute() * CryptoCompare::price(Network::currency(), Settings::currency());
        }
        // @codeCoverageIgnoreEnd

        return $marketCap;
    }
}
