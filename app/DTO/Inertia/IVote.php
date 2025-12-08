<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\Models\Transaction;
use App\ViewModels\TransactionViewModel;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('IVote')]
class IVote extends Data
{
    public function __construct(
        public string $hash,
    ) {
    }

    public static function fromModel(Transaction $transaction): self
    {
        $viewModel = new TransactionViewModel($transaction);

        return new self(
            hash: $viewModel->hash(),
        );
    }
}
