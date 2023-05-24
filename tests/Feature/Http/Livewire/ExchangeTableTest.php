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

it('sets the filter', function ($type) {
    Livewire::test(ExchangeTable::class)
        ->call('setFilter', 'type', $type)
        ->assertSet('type', $type);
})->with(['exchanges', 'pair']);
