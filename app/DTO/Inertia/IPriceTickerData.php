<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('IPriceTickerData')]
class IPriceTickerData extends Data
{
    public function __construct(
        public string $currency,
        public bool $isPriceAvailable,
        public ?float $priceExchangeRate,
    ) {
    }
}
