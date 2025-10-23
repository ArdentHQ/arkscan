<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('IConfigPagination')]
class IConfigPagination extends Data
{
    public function __construct(
        public int $per_page,
    ) {
    }
}
