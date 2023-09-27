<?php

declare(strict_types=1);

namespace App\Http\Livewire\Home;

use App\Actions\CacheNetworkHeight;
use App\Actions\CacheNetworkSupply;
use App\Facades\Network;
use App\Facades\Settings;
use App\Services\BigNumber;
use App\Services\Cache\Statistics as StatisticsCache;
use App\Services\MarketCap;
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
        $data = StatisticsCache::transactionData();

        return view('livewire.home.statistics', [
            'height'    => CacheNetworkHeight::execute(),
            'volume'    => BigNumber::new($data['volume'])->toFloat(),
            'supply'    => CacheNetworkSupply::execute() / 1e8,
            'marketCap' => MarketCap::getFormatted(Network::currency(), Settings::currency()),
        ]);
    }
}
