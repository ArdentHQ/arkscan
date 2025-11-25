<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use Closure;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('IRequestData')]
class IRequestData extends Data
{
    public function __construct(
        #[LiteralTypeScriptType('Record<string, ICurrency>')]
        public array $currencies,
        public INetwork $network,
        public IConfigProductivity $productivity,
        public ISettings $settings,
        public IConfigArkconnect $arkconnectConfig,
        public IConfigPagination $pagination,
        public string $broadcasting,
        #[LiteralTypeScriptType('string')]
        public Closure $networkName,
        #[LiteralTypeScriptType('boolean')]
        public Closure $isDownForMaintenance,
        #[LiteralTypeScriptType('boolean')]
        public Closure $isProduction,
        #[LiteralTypeScriptType('IPriceTickerData')]
        public Closure $priceTickerData,
        #[LiteralTypeScriptType('string')]
        public Closure $theme,
        #[LiteralTypeScriptType('string')]
        public Closure $mainnetExplorerUrl,
        #[LiteralTypeScriptType('string')]
        public Closure $testnetExplorerUrl,
    ) {
    }
}
