<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\Actions\CacheNetworkHeight;
use App\DTO\MemoryWallet;
use App\Models\Block as Model;
use App\Services\ExchangeRate;
use App\Services\Timestamp;
use App\ViewModels\BlockViewModel;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('IBlockDetails')]
class BlockDetails extends Data
{
    public function __construct(
        public string $hash,
        public int $height,
        public int $timestamp,
        public string $timestampFormatted,
        public int $transactionCount,
        public float $reward,
        public string $rewardFiat,
        public float $fee,
        public string $feeFiat,
        public float $totalReward,
        public string $totalRewardFiat,
        public int $confirmations,
        public string $validatorAddress,
        public ?string $validatorUsername,
        public bool $validatorHasUsername,
    ) {
    }

    public static function fromModel(Model $block): self
    {
        $viewModel = new BlockViewModel($block);
        $validator = MemoryWallet::fromAddress($block->proposer);

        return new self(
            hash: $block->hash,
            height: $block->number->toNumber(),
            timestamp: $block->timestamp,
            timestampFormatted: Timestamp::fromUnixHuman($block->timestamp),
            transactionCount: $block->transactions_count,
            reward: $block->reward->toFloat(),
            rewardFiat: ExchangeRate::convert($block->reward->toFloat(), $block->timestamp),
            fee: $block->fee->toFloat(),
            feeFiat: ExchangeRate::convert($block->fee->toFloat(), $block->timestamp),
            totalReward: $viewModel->totalReward(),
            totalRewardFiat: $viewModel->totalRewardFiat(),
            confirmations: abs(CacheNetworkHeight::execute() - $block->number->toNumber()),
            validatorAddress: $validator->address() ?? 'Genesis',
            validatorUsername: $validator->username(),
            validatorHasUsername: $validator->hasUsername(),
        );
    }
}
