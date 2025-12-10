<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\Models\ForgingStats as Model;
use App\ViewModels\ForgingStatsViewModel;
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

    public static function fromModel(Model $block): self
    {
        $viewModel = new ForgingStatsViewModel($block);
        $validator = $viewModel->validator();
        if ($validator !== null) {
            $validator = Wallet::fromModel($validator->model());
        }

        return new self(
            number: $block->missed_height,
            timestamp: $block->timestamp,
            validator: $validator,
            voterCount: $viewModel->validator()?->voterCount(),
            votesPercentage: $viewModel->validator()?->votesPercentage(),
            votes: $viewModel->validator()?->votes(),
        );
    }
}
