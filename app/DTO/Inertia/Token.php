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

    public static function fromModel(Model $exchange): self
    {
        return new self(
            address: $exchange->address,
            name: $exchange->name,
            symbol: $exchange->symbol,
            decimals: $exchange->decimals,
            totalSupply: (string) $exchange->total_supply,
            deploymentHash: $exchange->deployment_hash,
        );
    }
}
