<?php

declare(strict_types=1);

use App\Models\Exchange;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('migrate:fresh');
});

it('filter exchanges that have coingecko id', function () {
    $exchange = Exchange::factory()->create([
        'coingecko_id' => 'binance',
    ]);

    Exchange::factory()->create([
        'coingecko_id' => null,
    ]);

    $exchanges = Exchange::coingecko()->get();

    expect($exchanges->count())->toBe(1);

    expect($exchanges->first()->id)->toBe($exchange->id);
});
