<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use Spatie\LaravelData\Data;
use App\DTO\Inertia\INetwork;
use App\DTO\Inertia\ISettings;
use App\DTO\Inertia\IConfigArkconnect;
use App\DTO\Inertia\IConfigProductivity;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

#[TypeScript('IRequestData')]
class IRequestData extends Data
{
    public function __construct(
        #[LiteralTypeScriptType('Record<string, ICurrency>')]
        public array $currencies,
        public INetwork $network,
        public IConfigProductivity $productivity,
        public ISettings $settings,
        public IConfigArkconnect $arkconnect,
    ) {
    }

}
