<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Actions\CacheNetworkHeight;
use App\Actions\CacheNetworkSupply;
use App\Facades\Network;
use App\Facades\Settings;
use App\Services\MarketCap;
use Illuminate\View\View;
use Livewire\Component;

final class NetworkStatusBlock extends Component
{
    /** @phpstan-ignore-next-line */
    protected $listeners = [
        'currencyChanged' => '$refresh',
    ];

    public function render(): View
    {
        return view('livewire.network-status-block', [
            'height'      => CacheNetworkHeight::execute(),
            'network'     => Network::name(),
            'supply'      => CacheNetworkSupply::execute() / 1e8,
            'marketCap'   => MarketCap::getFormatted(Network::currency(), Settings::currency()),
        ]);
    }
}
