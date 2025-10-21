<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('ISettings')]
class ISettings extends Data
{
    public function __construct(
        public string $currency,
        public bool $priceChart,
        public bool $feeChart,
        public string $theme,
    ) {
    }
}
