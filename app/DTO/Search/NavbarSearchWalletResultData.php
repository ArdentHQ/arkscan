<?php

declare(strict_types=1);

namespace App\DTO\Search;

use App\DTO\MemoryWallet;
use App\Models\Wallet;
use App\ViewModels\WalletViewModel;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('INavbarSearchWalletResultData')]
final class NavbarSearchWalletResultData extends Data
{
    public function __construct(
        public string $address,
        public ?string $username,
        public bool $hasUsername,
        public bool $isKnown,
        public float $balance,
    ) {
    }

    public static function fromModel(Wallet $wallet): self
    {
        $memoryWallet = MemoryWallet::fromAddress($wallet->address);
        $viewModel = new WalletViewModel($wallet);

        return new self(
            address: $wallet->address,
            username: $memoryWallet->username(),
            hasUsername: $memoryWallet->hasUsername(),
            isKnown: $viewModel->isKnown(),
            balance: $viewModel->balance(),
        );
    }
}
