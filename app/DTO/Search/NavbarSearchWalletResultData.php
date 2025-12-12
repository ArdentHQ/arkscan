<?php

declare(strict_types=1);

namespace App\DTO\Search;

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

    public static function fromViewModel(WalletViewModel $wallet): self
    {
        return new self(
            address: $wallet->address(),
            username: $wallet->username(),
            hasUsername: $wallet->hasUsername(),
            isKnown: $wallet->isKnown(),
            balance: $wallet->balance(),
        );
    }
}
