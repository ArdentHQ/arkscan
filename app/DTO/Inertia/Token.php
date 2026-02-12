<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\Models\Token as Model;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('IToken')]
class Token extends Data
{
    private const MAX_NAME_LENGTH = 20;
    private const MAX_SYMBOL_LENGTH = 5;

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
            name: self::normalizeString($token->name, self::MAX_NAME_LENGTH), // Truncate to prevent unnecessary long names
            symbol: self::normalizeString($token->symbol, self::MAX_SYMBOL_LENGTH, '…'), // Truncate to prevent unnecessary long symbols
            decimals: $token->decimals,
            totalSupply: (string) $token->total_supply,
            deploymentHash: $token->deployment_hash,
        );
    }

    private static function normalizeString(string $value, int $maxLength, ?string $suffix = null): string {
        if (strlen($value) <= $maxLength) {
            return $value;
        }

        if (! Str::isAscii($value) && function_exists('mb_strimwidth')) {
            $value = mb_strimwidth($value, 0, $maxLength, '', 'UTF-8');
        } else {
            $value = substr($value, 0, $maxLength);
        }

        if ($suffix !== null) {
            return Str::trim($value) . $suffix;
        }

        return Str::trim($value);
    }
}
