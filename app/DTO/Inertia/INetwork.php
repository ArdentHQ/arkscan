<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('INetwork')]
class INetwork extends Data
{
    public function __construct(
        public string $coin,
        public string $name,
        public string $api,
        public string $alias,
        public string $nethash,
        public string $mainnetExplorerUrl,
        public string $testnetExplorerUrl,
        public string $legacyExplorerUrl,
        public string $currency,
        public string $currencySymbol,
        public int $confirmations,
        public array $knownWallets,
        public string $knownWalletsUrl,
        public bool $canBeExchanged,
        public string $epoch,
        public int $validatorCount,
        public int $blockTime,
        public int $blockReward,
        public int $base58Prefix,
        #[LiteralTypeScriptType('{
            consensus: string;
            multipayment: string;
            username: string;
        }')]
        public array $contractAddresses,
        #[LiteralTypeScriptType('{
            transfer: string;
            multipayment: string;
            vote: string;
            unvote: string;
            validator_registration: string;
            validator_resignation: string;
            validator_update: string;
            username_registration: string;
            username_resignation: string;
            contract_deployment: string;
        }')]
        public array $contractMethods,
    ) {
    }

    
}
