<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class MarketData
{
    public function __construct(private float $price, private float $priceChange)
    {
    }

    public static function fromCoinGeckoApiResponse(string $baseCurrency, array $data): self
    {
        return new static(
            price: Arr::get($data, 'market_data.current_price.'.Str::lower($baseCurrency)),
            priceChange: Arr::get($data, 'market_data.price_change_percentage_24h_in_currency.'.Str::lower($baseCurrency), 0) / 100,
        );
    }

    public static function fromCryptoCompareApiResponse(string $baseCurrency, string $targetCurrency, array $data): self
    {
        return new static(
            price: Arr::get($data, 'RAW.'.$baseCurrency.'.'.strtoupper($targetCurrency).'.PRICE', 0),
            priceChange: Arr::get($data, 'RAW.'.$baseCurrency.'.'.strtoupper($targetCurrency).'.CHANGEPCT24HOUR', 0) / 100,
        );
    }

    public function price(): float
    {
        return $this->price;
    }

    public function priceChange(): float
    {
        return $this->priceChange;
    }
}
