<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\Models\Token as Model;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('IToken')]
class Token extends Data
{
    public function __construct(
        public string $address,
        public string $name,
        public string $symbol,
        public int $decimals,
        public string $totalSupply,
        public string $deploymentHash,
    ) {
    }

    public static function fromModel(Model $token): self
    {
        return new self(
            address: $token->address,
            name: $token->name,
            symbol: $token->symbol,
            decimals: $token->decimals,
            totalSupply: (string) $token->total_supply,
            deploymentHash: $token->deployment_hash,
        );
    }
}
