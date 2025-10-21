<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use Spatie\LaravelData\Data;
use App\DTO\Inertia\ISettings;
use App\DTO\Inertia\IConfigArkconnect;
use App\DTO\Inertia\IConfigProductivity;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('IRequestData')]
class IRequestData extends Data
{
    public function __construct(
        #[LiteralTypeScriptType('Record<string, ICurrency>')]
        public array $currencies,
        // @TODO: Add INetwork interface
        public array $network,
        public IConfigProductivity $productivity,
        public ISettings $settings,
        public IConfigArkconnect $arkconnect,
    ) {
    }

}
