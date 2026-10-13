<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Support\Arr;

final class MarketData
{
    public function __construct(private readonly float $price, private readonly float $priceChange)
    {
    }

    public static function fromArkPricingApiResponse(array $data): self
    {
        return new self(
            price: Arr::get($data, 'price', 0),
            priceChange: Arr::get($data, 'change24h', 0) / 100,
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
