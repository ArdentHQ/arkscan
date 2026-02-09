<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\Models\TokenTransfer as Model;
use App\Services\Cache\WalletCache;
use ArkEcosystem\Crypto\Utils\UnitConverter;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('ITokenTransfer')]
class TokenTransfer extends Data
{
    public function __construct(
        public string $transaction_hash,
        public string $from,
        public string $to,
        public ?string $toUsername,
        public float $amount,
        public string $value,
        public int $block_number,
        public int $index,
        public Token $token,
        public ?Transaction $transaction = null,
    ) {
    }

    public static function fromModel(Model $transfer): self
    {
        $toUsername = (new WalletCache())->getWalletNameByAddress($transfer->to);

        return new self(
            transaction_hash: $transfer->transaction_hash,
            from: $transfer->from,
            to: $transfer->to,
            toUsername: $toUsername,
            amount: UnitConverter::formatUnits((string) $transfer->value, 'ark'),
            value: (string) $transfer->value,
            block_number: $transfer->block_number,
            index: $transfer->index,
            token: Token::fromModel($transfer->token),
            transaction: $transfer->transaction !== null ? Transaction::fromModel($transfer->transaction, $transfer->to) : null,
        );
    }
}
