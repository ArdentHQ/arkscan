<?php

declare(strict_types=1);

namespace  App\Http\Livewire;

use App\Facades\Network;
use App\Services\Blockchain\NetworkStatus;
use App\Services\CryptoCompare;
use App\Services\NumberFormatter;
use Livewire\Component;

final class NetworkStatusBlock extends Component
{
    public function render()
    {
        $marketCap = 0;

        if (Network::canBeExchanged()) {
            $marketCap = NetworkStatus::supply() * CryptoCompare::price(Network::currency(), 'USD'); // @TODO: use currency from settings
        }

        return view('livewire.network-status-block', [
            'height'    => NumberFormatter::number(NetworkStatus::height()),
            'network'   => Network::name(),
            'supply'    => NumberFormatter::currencyWithSymbol(NetworkStatus::supply(), Network::currencySymbol()),
            'marketCap' => NumberFormatter::currency($marketCap, 'USD'), // @TODO: use currency from settings
        ]);
    }
}
