<?php

declare(strict_types=1);

use App\Http\Livewire\PriceStats;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

it('should render the price change', function () {
    Http::fake([
        'cryptocompare.com/*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histohour.json')), true)),
    ]);

    Livewire::test(PriceStats::class)
        ->assertSee('13.70%');
});

it('should render the values', function () {
    Http::fake([
        'cryptocompare.com/*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histohour.json')), true)),
    ]);

    Livewire::test(PriceStats::class, ['placeholder' => true])
        ->assertSee('[4,5,2,2,2,3,5,1,4,5,6,5,3,3,4,5,6,4,4,4,5,8,8,10]');
});

it('handle price change when price is zero', function () {
    $response = json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histohour.json')), true);
    // Force 0 price
    $response['Data'] = collect($response['Data'])->map(fn ($item) => array_merge($item, ['close' => 0]))->toArray();

    Http::fake([
        'cryptocompare.com/*' => Http::response($response),
    ]);

    Livewire::test(PriceStats::class)->assertSee('0.00%');
});
