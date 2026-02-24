<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\Models\ForgingStats as Model;
use App\ViewModels\WalletViewModel;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('IForgingStats')]
class ForgingStats extends Data
{
    public function __construct(
        public int $number,
        public int $timestamp,
        public ?Wallet $validator,
        public ?int $voterCount,
        public ?float $votesPercentage,
        public ?float $votes,
    ) {
    }

    public static function fromModel(Model $forgingStats): self
    {
        $validatorWallet = $forgingStats->validator;

        $walletViewModel = $validatorWallet !== null
            ? new WalletViewModel($validatorWallet)
            : null;

        return new self(
            number: $forgingStats->missed_height,
            timestamp: $forgingStats->timestamp,
            validator: $validatorWallet !== null ? Wallet::fromModel($validatorWallet) : null,
            voterCount: $walletViewModel?->voterCount(),
            votesPercentage: $walletViewModel?->votesPercentage(),
            votes: $walletViewModel?->votes(),
        );
    }
}
