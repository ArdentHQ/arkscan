<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\Models\Wallet as Model;
use App\ViewModels\WalletViewModel;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('IWalletReference')]
class WalletReference extends Data
{
    public function __construct(
        public string $address,
        public ?string $username,
    ) {
    }

    public static function stub(string $address): self
    {
        return new self(
            address: $address,
            username: null,
        );
    }

    public static function fromModel(Model $wallet): self
    {
        $viewModel = new WalletViewModel($wallet);

        return new self(
            address: $wallet->address,
            username: $viewModel->username(),
        );
    }
}
