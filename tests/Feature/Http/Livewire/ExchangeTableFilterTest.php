<?php

declare(strict_types=1);

use App\Http\Livewire\ExchangeTableFilter;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;

beforeEach(function () {
    Artisan::call('migrate:fresh');
});

it('renders the component', function () {
    Livewire::test(ExchangeTableFilter::class)
        ->assertSee(trans('general.all'))
        ->assertSee(trans('pages.exchanges.type.exchanges'))
        ->assertSee(trans('pages.exchanges.type.agreggators'))
        ->assertSee(trans('pages.exchanges.pair.btc'))
        ->assertSee(trans('pages.exchanges.pair.eth'))
        ->assertSee(trans('pages.exchanges.pair.stablecoins'))
        ->assertSee(trans('pages.exchanges.pair.other'));
});

it('sets the filter', function () {
    Livewire::test(ExchangeTableFilter::class)
        ->call('setFilter', 'type', 'exchanges')
        ->assertSet('type', 'exchanges')
        ->assertDispatched('filterChanged', 'type', 'exchanges')
        ->call('setFilter', 'pair', 'btc')
        ->assertSet('pair', 'btc')
        ->assertDispatched('filterChanged', 'pair', 'btc');
});

it('uses the query string', function () {
    Livewire::withQueryParams([
            'type' => 'exchanges',
            'pair' => 'btc',
    ])
        ->test(ExchangeTableFilter::class)
        ->assertSet('type', 'exchanges')
        ->assertSet('pair', 'btc');
});
