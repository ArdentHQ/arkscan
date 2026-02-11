<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('IConfigArkconnect')]
class IConfigArkconnect extends Data
{
    public function __construct(
        public bool $enabled,
        public string $vaultUrl,
    ) {
    }
}
