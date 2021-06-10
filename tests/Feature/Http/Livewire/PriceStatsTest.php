<?php

declare(strict_types=1);

use App\Http\Livewire\PriceStats;
use App\Services\Cache\NetworkStatusBlockCache;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

it('should render the values', function () {
    Config::set('explorer.networks.development.canBeExchanged', true);

    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 1);
    (new NetworkStatusBlockCache())->setMarketCap('DARK', 'USD', 1);
    (new NetworkStatusBlockCache())->setHistoricalHourly('DARK', 'USD', collect(['1.898', '1.904', '1.967', '1.941', '2.013', '2.213', '2.414', '2.369', '2.469', '2.374', '2.228', '2.211', '2.266', '2.364', '2.341', '2.269', '1.981', '1.889', '1.275', '1.471', '1.498', '1.518', '1.61', '1.638']));

    Livewire::test(PriceStats::class)
        ->assertSee('["1.898","1.904","1.967","1.941","2.013","2.213","2.414","2.369","2.469","2.374","2.228","2.211","2.266","2.364","2.341","2.269","1.981","1.889","1.275","1.471","1.498","1.518","1.61","1.638"]');
});

it('should render the placeholder values when no price cached yet', function () {
    Config::set('explorer.networks.development.canBeExchanged', true);

    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', null);
    (new NetworkStatusBlockCache())->setHistoricalHourly('DARK', 'USD', collect(['1.898', '1.904', '1.967', '1.941', '2.013', '2.213', '2.414', '2.369', '2.469', '2.374', '2.228', '2.211', '2.266', '2.364', '2.341', '2.269', '1.981', '1.889', '1.275', '1.471', '1.498', '1.518', '1.61', '1.638']));

    Livewire::test(PriceStats::class)
        ->assertSee('[4,5,2,2,2,3,5,1,4,5,6,5,3,3,4,5,6,4,4,4,5,8,8,10]');
});

it('should render the placeholder values when no historial data yet', function () {
    Config::set('explorer.networks.development.canBeExchanged', true);

    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 1);
    (new NetworkStatusBlockCache())->setHistoricalHourly('DARK', 'USD', null);

    Livewire::test(PriceStats::class)
        ->assertSee('[4,5,2,2,2,3,5,1,4,5,6,5,3,3,4,5,6,4,4,4,5,8,8,10]');
});

it('should render the placeholder values when cannot be exchanged', function () {
    Config::set('explorer.networks.development.canBeExchanged', false);

    Livewire::test(PriceStats::class)
        ->assertSee('[4,5,2,2,2,3,5,1,4,5,6,5,3,3,4,5,6,4,4,4,5,8,8,10]');
});
