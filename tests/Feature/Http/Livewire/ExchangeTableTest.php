<?php

declare(strict_types=1);

use App\Http\Livewire\ExchangeTable;
use App\Models\Exchange;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;

beforeEach(function () {
    Artisan::call('migrate:fresh');
});

it('should render', function () {
    Exchange::factory()->create([
        'name'        => '7b',
        'url'         => 'https://7b.com',
        'btc'         => true,
        'eth'         => true,
        'stablecoins' => true,
        'other'       => false,
    ]);

    Livewire::test(ExchangeTable::class)
        ->assertSee('7b')
        ->assertSee('https://7b.com')
        ->assertSeeInOrder([
            'BTC',
            'ETH',
            'Stablecoins',
        ]);
});

it('uses the query string', function () {
    Livewire::withQueryParams([
            'type' => 'exchanges',
            'pair' => 'btc',
    ])
        ->test(ExchangeTable::class)
        ->assertSet('type', 'exchanges')
        ->assertSet('pair', 'btc');
});

it('sets the filter', function () {
    Livewire::test(ExchangeTable::class)
        ->call('setFilter', 'type', 'exchanges')
        ->assertSet('type', 'exchanges');
});

it('sets the pair', function () {
    Livewire::test(ExchangeTable::class)
        ->call('setFilter', 'pair', 'btc')
        ->assertSet('pair', 'btc');
});

it('ignores an invalid filter', function () {
    Livewire::test(ExchangeTable::class)
        ->call('setFilter', 'invalid', 'btc')
        ->assertSet('pair', 'all')
        ->assertSet('type', 'all');
});
