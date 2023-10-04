<?php

declare(strict_types=1);

use App\Facades\Settings;
use App\Http\Livewire\Home\Chart;
use App\Services\Cache\NetworkCache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use function Tests\fakeCryptoCompare;

beforeEach(function (): void {
    Carbon::setTestNow('2020-01-01 00:00:00');
});

it('should render the component with fiat value', function () {
    fakeCryptoCompare();

    Config::set('arkscan.networks.development.canBeExchanged', true);
    Config::set('arkscan.networks.development.currency', 'ARK');

    Artisan::call('explorer:cache-currencies-data');
    Artisan::call('explorer:cache-currencies-history --no-delay');
    Artisan::call('explorer:cache-prices');

    (new NetworkCache())->setSupply(fn () => 456748578.342 * 1e8);

    Livewire::test(Chart::class)
        ->set('period', 'day')
        ->assertSee('$1.22 USD')
        ->assertSee('[1.898,1.904,1.967,1.941,2.013,2.213,2.414,2.369,2.469,2.374,2.228,2.211,2.266,2.364,2.341,2.269,1.981,1.889,1.275,1.471,1.498,1.518,1.61,1.638]');
});

it('should render the component with non fiat value', function () {
    Settings::shouldReceive('all')->andReturn(Settings::all());
    Settings::shouldReceive('theme')->andReturn('light');
    Settings::shouldReceive('currency')->andReturn('BTC');

    fakeCryptoCompare(false, 'BTC');

    Config::set('arkscan.networks.development.canBeExchanged', true);
    Config::set('arkscan.networks.development.currency', 'ARK');

    Artisan::call('explorer:cache-currencies-data');
    Artisan::call('explorer:cache-currencies-history --no-delay');
    Artisan::call('explorer:cache-prices');

    (new NetworkCache())->setSupply(fn () => 456748578.342 * 1e8);

    Livewire::test(Chart::class)
        ->set('period', 'day')
        ->assertSee('0.00003363 BTC')
        ->assertSee('[1.898,1.904,1.967,1.941,2.013,2.213,2.414,2.369,2.469,2.374,2.228,2.211,2.266,2.364,2.341,2.269,1.981,1.889,1.275,1.471,1.498,1.518,1.61,1.638]');
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
