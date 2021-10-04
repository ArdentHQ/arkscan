<?php

declare(strict_types=1);

use App\Services\MarketDataProviders\CryptoCompare;
use Illuminate\Support\Facades\Http;
use function Spatie\Snapshots\assertMatchesSnapshot;
use function Tests\fakeCryptoCompare;

it('should fetch the price data for the given collection', function () {
    fakeCryptoCompare();

    $dto = (new CryptoCompare())->priceAndPriceChange('ARK', collect(['USD']))->get('USD');

    expect($dto->priceChange())->toEqual(0.14989143413680925);

    expect($dto->price())->toEqual(1.2219981765);
});

it('should fetch the historical prices for the given pair', function () {
    Http::fake([
        'cryptocompare.com/*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/historical.json')), true)),
    ]);

    assertMatchesSnapshot((new CryptoCompare())->historical('ARK', 'USD'));
});

it('should fetch the historical prices per hour for the given pair', function () {
    Http::fake([
        'cryptocompare.com/*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histohour.json')), true)),
    ]);

    assertMatchesSnapshot((new CryptoCompare())->historicalHourly('ARK', 'USD'));
});
