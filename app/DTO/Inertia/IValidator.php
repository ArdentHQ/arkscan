<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\Models\Wallet as Model;
use App\ViewModels\WalletViewModel;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('IValidator')]
class IValidator extends Data
{
    public function __construct(
        public ?int $rank,
        public string $address,
        public bool $isActive,
        public bool $isDormant,
        public bool $isResigned,
        public ?string $username,
        public bool $hasUsername,
        public int $voterCount,
        public float $votes,
        public float $votesPercentage,
        public int $missedBlocks,
        #[LiteralTypeScriptType('"success" | "warning" | "danger" | "inactive"')]
        public string $missedBlocksState,
        public ?string $voteUrl,
    ) {
    }

    public static function fromModel(Model $wallet): self
    {
        $viewModel   = new WalletViewModel($wallet);

        $voteUrl = null;
        if ($viewModel->isValidator() && $wallet->public_key !== null) {
            $voteUrl = $viewModel->voteUrl();
        }

        $missedBlocksState = 'success';

        $missedBlocks = $viewModel->missedBlocks();

        if ($viewModel->isActive()) {
            $missedPercentage = $viewModel->productivity();

            if ($missedPercentage < config('arkscan.productivity.danger')) {
                $missedBlocksState = 'danger';
            } elseif ($missedPercentage < config('arkscan.productivity.warning')) {
                $missedBlocksState = 'warning';
            }
        } else {
            $missedBlocksState = 'inactive';
        }

        return new self(
            rank: $viewModel->rank(),
            address: $wallet->address,
            isActive: $viewModel->isActive(),
            isDormant: $viewModel->isDormant(),
            isResigned: $viewModel->isResigned(),
            username: $viewModel->username(),
            hasUsername: $viewModel->hasUsername(),
            votes: $viewModel->votes(),
            votesPercentage: $viewModel->votesPercentage(),
            missedBlocks: $missedBlocks,
            missedBlocksState: $missedBlocksState,
            voteUrl: $voteUrl,
            voterCount: $viewModel->voterCount(),
        );
    }
}
