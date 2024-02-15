<?php

declare(strict_types=1);

use App\Contracts\Network as NetworkContract;
use App\Facades\Settings;
use App\Http\Livewire\Home\Chart;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\NetworkStatusBlockCache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use function Tests\fakeCryptoCompare;
use Tests\Feature\Http\Livewire\__stubs\NetworkStub;

beforeEach(function (): void {
    Carbon::setTestNow('2020-01-01 00:00:00');
});

it('should render the component with fiat value', function () {
    fakeCryptoCompare();

    Config::set('arkscan.networks.development.canBeExchanged', true);
    Config::set('arkscan.networks.development.currency', 'ARK');

    Artisan::call('explorer:cache-currencies-data');
    Artisan::call('explorer:cache-prices');

    (new NetworkStatusBlockCache())->setPrice('ARK', 'USD', 1.4);

    (new NetworkCache())->setSupply(fn () => 456748578.342 * 1e8);

    Livewire::test(Chart::class)
        ->set('period', 'day')
        ->assertSee('[1.898,1.904,1.967,1.941,2.013,2.213,2.414,2.369,2.469,2.374,2.228,2.211,2.266,2.364,2.341,2.269,1.981,1.889,1.275,1.471,1.498,1.518,1.61,1.4]');
});

it('should render the component with non fiat value', function () {
    Settings::shouldReceive('all')->andReturn(Settings::all());
    Settings::shouldReceive('theme')->andReturn('light');
    Settings::shouldReceive('currency')->andReturn('BTC');

    fakeCryptoCompare(false, 'BTC');

    Config::set('arkscan.networks.development.canBeExchanged', true);
    Config::set('arkscan.networks.development.currency', 'ARK');

    Artisan::call('explorer:cache-currencies-data');
    Artisan::call('explorer:cache-prices');

    (new NetworkStatusBlockCache())->setPrice('ARK', 'BTC', 15);

    (new NetworkCache())->setSupply(fn () => 456748578.342 * 1e8);

    Livewire::test(Chart::class)
        ->set('period', 'day')
        ->assertSee('[1.898,1.904,1.967,1.941,2.013,2.213,2.414,2.369,2.469,2.374,2.228,2.211,2.266,2.364,2.341,2.269,1.981,1.889,1.275,1.471,1.498,1.518,1.61,15]');
});

it('should change the period', function ($period) {
    Livewire::test(Chart::class)
        ->call('setPeriod', $period)
        ->assertSet('period', $period);
})->with([
    'all',
    'day',
    'week',
    'month',
    'year',
]);

it('should not change the period with an invalid value', function ($period) {
    Livewire::test(Chart::class)
        ->set('period', 'day')
        ->call('setPeriod', $period)
        ->assertSet('period', 'day');
})->with([
    'quarter',
    'decade',
    'random-value',
]);

it('should handle events', function () {
    $networkStub = new NetworkStub(false);
    app()->singleton(NetworkContract::class, fn () => $networkStub);

    $component = Livewire::test(Chart::class)
        ->assertDontSee(route('exchanges'));

    $networkStub->canBeExchanged = true;
    $component->emit('currencyChanged')
        ->assertSee(route('exchanges'));

    $networkStub->canBeExchanged = false;
    $component->emit('themeChanged')
        ->assertDontSee(route('exchanges'));

    $networkStub->canBeExchanged = true;
    $component->emit('updateChart')
        ->assertSee(route('exchanges'));
});
