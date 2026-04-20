<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\Models\TokenHolder as Model;
use ArkEcosystem\Crypto\Utils\UnitConverter;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('ITokenHolder')]
class TokenHolder extends Data
{
    public function __construct(
        public MemoryWallet $wallet,
        public Token $token,
        public float $balance,
    ) {
    }

    public static function fromModel(Model $holder): self
    {
        return new self(
            wallet: MemoryWallet::fromAddress($holder->address),
            token: Token::fromModel($holder->token),
            balance: UnitConverter::formatUnits((string) $holder->balance, 'ark')->toFloat(),
        );
    }
}
