<?php

declare(strict_types=1);

use App\Http\Livewire\ExchangeTable;
use App\Models\Exchange;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;

it('should render', function () {
    Artisan::call('migrate:fresh');

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
