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

it('should make an instance from the ark-pricing response', function () {
    $response = [];

    Arr::set($response, 'price', 11.21);
    Arr::set($response, 'change24h', 1.62 * 100);

    $subject = MarketData::fromArkPricingApiResponse($response);

    expect($subject->price())->toBe(11.21);
    expect($subject->priceChange())->toBe(1.62);
});
