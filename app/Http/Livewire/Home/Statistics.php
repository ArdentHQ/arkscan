<?php

declare(strict_types=1);

namespace App\Http\Livewire\Home;

use App\Actions\CacheNetworkHeight;
use App\Actions\CacheNetworkSupply;
use App\Facades\Network;
use App\Facades\Settings;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\Statistics as StatisticsCache;
use App\Services\MarketCap;
use App\Services\NumberFormatter;
use ARKEcosystem\Foundation\NumberFormatter\NumberFormatter as BetterNumberFormatter;
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
            'volume'    => $this->formatVolume((new CryptoDataCache())->getVolume(Settings::currency()), Settings::currency()),
            'supply'    => CacheNetworkSupply::execute() / 1e8,
            'marketCap' => MarketCap::getFormatted(Network::currency(), Settings::currency()),
        ]);
    }

    private function formatVolume($volume, $target)
    {
        if (NumberFormatter::isFiat($volume)) {
            return trim(trim(NumberFormatter::currencyWithDecimals($volume, $target, 0), '0'), '.');
        }

        return BetterNumberFormatter::new()
            ->formatWithCurrencyCustom(
                $volume,
                $target,
                NumberFormatter::CRYPTO_DECIMALS
            );
    }
}
