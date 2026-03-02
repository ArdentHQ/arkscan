<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\Actions\CacheNetworkHeight;
use App\DTO\Inertia\MemoryWallet as MemoryWalletDTO;
use App\Models\Block as Model;
use App\Services\ExchangeRate;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('IBlock')]
class Block extends Data
{
    public function __construct(
        public string $hash,
        public int $number,
        public int $timestamp,
        public int $transactionCount,
        public float $reward,
        public float $fee,
        #[LiteralTypeScriptType('Record<string, number>')]
        public array $exchangeRates,
        public int $confirmations,
        public MemoryWalletDTO $proposer,
    ) {
    }

    public static function fromModel(Model $block): self
    {
        return new self(
            hash: $block->hash,
            number: $block->number->toNumber(),
            timestamp: $block->timestamp,
            transactionCount: $block->transactions_count,
            reward: $block->reward->toFloat(),
            fee: $block->fee->toFloat(),
            exchangeRates: ExchangeRate::allCurrencyRates($block->timestamp),
            confirmations: abs(CacheNetworkHeight::execute() - $block->number->toNumber()),
            proposer: MemoryWalletDTO::fromAddress($block->proposer),
        );
    }
}
