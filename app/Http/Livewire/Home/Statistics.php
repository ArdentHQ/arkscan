<?php

declare(strict_types=1);

namespace App\Http\Livewire\Home;

use App\Actions\CacheNetworkHeight;
use App\Actions\CacheNetworkSupply;
use App\Facades\Network;
use App\Facades\Settings;
use App\Services\Cache\CryptoDataCache;
use App\Services\MarketCap;
use App\Services\NumberFormatter;
use Illuminate\View\View;
use Livewire\Component;

final class Statistics extends Component
{
    /** @phpstan-ignore-next-line */
    protected $listeners = [
        'currencyChanged' => '$refresh',
    ];

    public function render(): View
    {
        $volume = (new CryptoDataCache())->getVolume(Settings::currency());

        return view('livewire.home.statistics', [
            'height'    => CacheNetworkHeight::execute(),
            'volume'    => NumberFormatter::currencyForViews($volume ?? 0, Settings::currency()),
            'supply'    => CacheNetworkSupply::execute() / 1e8,
            'marketCap' => MarketCap::getFormatted(Network::currency(), Settings::currency()),
        ]);
    }
}
