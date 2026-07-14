<?php

declare(strict_types=1);

use App\DTO\MarketData;
use Illuminate\Support\Arr;

it('should make an instance that has all properties', function () {
    $subject = new MarketData(
        price: 11.21,
        priceChange: 1.62
    );

    expect($subject->price())->toBe(11.21);
    expect($subject->priceChange())->toBe(1.62);
});

it('should make an instance from the coingecko response', function () {
    $response = [];

    Arr::set($response, 'market_data.current_price.usd', 11.21);
    Arr::set($response, 'market_data.price_change_percentage_24h_in_currency.usd', 1.62 * 100);

    $subject = MarketData::fromCoinGeckoApiResponse('USD', $response);

    expect($subject->price())->toBe(11.21);
    expect($subject->priceChange())->toBe(1.62);
});

it('should return null if the coingecko response has no price', function () {
    $response = [];

    Arr::set($response, 'market_data.current_price.usd', null);
    Arr::set($response, 'market_data.price_change_percentage_24h_in_currency.usd', 1.62 * 100);

    expect(MarketData::fromCoinGeckoApiResponse('USD', $response))->toBeNull();
});

it('should default the price change to zero if missing on the coingecko response', function () {
    $response = [];

    Arr::set($response, 'market_data.current_price.usd', 11.21);
    Arr::set($response, 'market_data.price_change_percentage_24h_in_currency.usd', null);

    $subject = MarketData::fromCoinGeckoApiResponse('USD', $response);

    expect($subject->price())->toBe(11.21);
    expect($subject->priceChange())->toBe(0.0);
});

it('should make an instance from the cryptocompare response', function () {
    $response = [];

    Arr::set($response, 'RAW.ARK.USD.PRICE', 11.21);
    Arr::set($response, 'RAW.ARK.USD.CHANGEPCT24HOUR', 1.62 * 100);

    $subject = MarketData::fromCryptoCompareApiResponse('ARK', 'USD', $response);

    expect($subject->price())->toBe(11.21);
    expect($subject->priceChange())->toBe(1.62);
});
