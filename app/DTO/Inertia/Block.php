<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\Models\Block as Model;
use App\ViewModels\BlockViewModel;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('IBlock')]
class Block extends Data
{
    public function __construct(
        public string $hash,
        public int $number,
        public int $timestamp,
        public int $transactionCount,
        public float $totalReward,
        public string $totalRewardFiat,
        public string $rewardFiat,
    ) {
    }

    public static function fromModel(Model $block): self
    {
        $viewModel = new BlockViewModel($block);

        return new self(
            hash: $block->hash,
            number: $block->number->toNumber(),
            timestamp: $block->timestamp,
            transactionCount: $block->transactions_count,
            totalReward: $viewModel->totalReward(),
            totalRewardFiat: $viewModel->totalRewardFiat(),
            rewardFiat: $viewModel->rewardFiat(),
        );
    }
}
