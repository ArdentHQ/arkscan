<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\Facades\Settings;
use App\Models\Exchange as Model;
use App\Services\ExchangeRate;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('IExchange')]
class IExchange extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $url,
        public bool $is_exchange,
        public bool $is_aggregator,
        public bool $btc,
        public bool $eth,
        public bool $stablecoins,
        public bool $other,
        public string $icon,
        public string $iconUrl,
        public ?string $coingecko_id,
        public ?float $price,
        public ?float $priceFiat,
        public ?float $volume,
        public ?float $volumeFiat,
    ) {
    }

    public static function fromModel(Model $exchange): self
    {
        return new self(
            id: $exchange->id,
            name: $exchange->name,
            url: $exchange->url,
            is_exchange: $exchange->is_exchange,
            is_aggregator: $exchange->is_aggregator,
            btc: $exchange->btc,
            eth: $exchange->eth,
            stablecoins: $exchange->stablecoins,
            other: $exchange->other,
            icon: $exchange->icon,
            iconUrl: config('arkscan.exchanges.icon_url').$exchange->icon.'.svg',
            coingecko_id: $exchange->coingecko_id,
            price: $exchange->price,
            priceFiat: $exchange->price !== null ? ExchangeRate::convertFiatToCurrencyNumerical($exchange->price, 'USD', Settings::currency()) : null,
            volume: $exchange->volume,
            volumeFiat: $exchange->volume !== null ? ExchangeRate::convertFiatToCurrencyNumerical($exchange->volume, 'USD', Settings::currency()) : null,
        );
    }
}
