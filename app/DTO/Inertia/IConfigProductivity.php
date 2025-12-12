<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('IConfigProductivity')]
class IConfigProductivity extends Data
{
    public function __construct(
        public float $danger,
        public float $warning,
    ) {
    }
}
