<?php

declare(strict_types=1);

use App\Http\Livewire\ExchangeTable;
use Livewire\Livewire;

it('should render', function () {
    Livewire::test(ExchangeTable::class)
        ->assertSee('7b')
        ->assertSee('$0.34')
        ->assertSee('$2,350,503.97')
        ->assertSee('https://7b.com')
        ->assertSeeInOrder([
            'BTC',
            'ETH',
            'Stablecoins',
        ]);
});

it('should have a computed exchanges property', function () {
    Livewire::test(ExchangeTable::class)
        ->assertSet('exchanges.0', [
            'name'   => '7b',
            'icon'   => 'app-exchanges.7b',
            'price'  => '0.34100',
            'volume' => '2350503.97',
            'url'    => 'https://7b.com',

            'pairs' => [
                'BTC',
                'ETH',
                'Stablecoins',
            ],
        ]);
});
