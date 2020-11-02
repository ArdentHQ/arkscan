<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Network;
use App\Services\Cache\NetworkCache;
use App\Services\CryptoCompare;
use App\Services\Settings;
use Illuminate\View\View;
use Livewire\Component;

final class NetworkStatusBlock extends Component
{
    public function render(): View
    {
        $marketCap = 0;

        // @codeCoverageIgnoreStart
        if (Network::canBeExchanged()) {
            $marketCap = (new NetworkCache())->getSupply() * CryptoCompare::price(Network::currency(), Settings::currency());
        }
        // @codeCoverageIgnoreEnd

        return view('livewire.network-status-block', [
            'height'    => (new NetworkCache())->getHeight(),
            'network'   => Network::name(),
            'supply'    => (new NetworkCache())->getSupply() / 1e8,
            'marketCap' => $marketCap,
        ]);
    }
}
