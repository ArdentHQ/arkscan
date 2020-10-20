<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Network;
use App\Services\Blockchain\NetworkStatus;
use App\Services\CryptoCompare;
use App\Services\NumberFormatter;
use App\Services\Settings;
use Livewire\Component;

final class NetworkStatusBlock extends Component
{
    public function render()
    {
        $marketCap = 0;

        if (Network::canBeExchanged()) {
            $marketCap = NetworkStatus::supply() * CryptoCompare::price(Network::currency(), Settings::currency());
        }

        return view('livewire.network-status-block', [
            'height'    => NumberFormatter::number(NetworkStatus::height()),
            'network'   => Network::name(),
            'supply'    => NumberFormatter::currency(NetworkStatus::supply(), Network::currency()),
            'marketCap' => NumberFormatter::currency($marketCap, Settings::currency()),
        ]);
    }
}
