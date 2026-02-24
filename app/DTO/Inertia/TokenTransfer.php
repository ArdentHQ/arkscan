<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\DTO\Inertia\Transaction as TransactionDTO;
use App\Models\TokenTransfer as Model;
use App\Models\Transaction;
use ArkEcosystem\Crypto\Utils\UnitConverter;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('ITokenTransfer')]
class TokenTransfer extends Data
{
    public function __construct(
        public string $transaction_hash,
        public MemoryWallet $from,
        public MemoryWallet $to,
        public float $amount,
        public string $value,
        public int $block_number,
        public int $index,
        public Token $token,
        public TransactionDTO $transaction,
    ) {
    }

    public static function fromModel(Model $transfer): self
    {
        /** @var Transaction $transaction */
        $transaction = $transfer->transaction;

        return new self(
            transaction_hash: $transfer->transaction_hash,
            from: MemoryWallet::fromAddress($transfer->from),
            to: MemoryWallet::fromAddress($transfer->to),
            amount: UnitConverter::formatUnits((string) $transfer->value, 'ark'),
            value: (string) $transfer->value,
            block_number: $transfer->block_number,
            index: $transfer->index,
            token: Token::fromModel($transfer->token),
            transaction: TransactionDTO::fromModel($transaction, $transfer->to),
        );
    }
}
