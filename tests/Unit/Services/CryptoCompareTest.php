<?php

declare(strict_types=1);

use App\Services\CryptoCompare;
use Illuminate\Support\Facades\Http;

use function Spatie\Snapshots\assertMatchesSnapshot;
use function Tests\fakeCryptoCompare;

it('should fetch the price data for the given collection', function () {
    fakeCryptoCompare();

    expect(CryptoCompare::getCurrenciesData('ARK', collect(['USD'])))->toEqual(collect([
        'USD' => [
            'priceChange' => 0.14989143413680925,
            'price'       => 1.2219981765,
            'marketCap'   => 192865161.6011891,
        ],
    ]));
});

it('should fetch the historical prices for the given pair', function () {
    Http::fake([
        'cryptocompare.com/*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/historical.json')), true)),
    ]);

    assertMatchesSnapshot(CryptoCompare::historical('ARK', 'USD'));
});

it('should fetch the historical prices per hour for the given pair', function () {
    Http::fake([
        'cryptocompare.com/*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histohour.json')), true)),
    ]);

    assertMatchesSnapshot(CryptoCompare::historicalHourly('ARK', 'USD'));
});
