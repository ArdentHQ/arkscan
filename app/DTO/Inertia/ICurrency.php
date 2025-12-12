<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('ICurrency')]
class ICurrency extends Data
{
    public function __construct(
        public string $currency,
        public ?string $locale,
        public ?string $symbol,
    ) {
    }
}
