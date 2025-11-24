<?php

declare(strict_types=1);

namespace App\DTO\Search;

use App\DTO\MemoryWallet;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('INavbarSearchMemoryWallet')]
final class NavbarSearchMemoryWalletData extends Data
{
    public function __construct(
        public ?string $address,
        public ?string $username,
        public bool $isContract,
    ) {
    }

    public static function fromMemoryWallet(?MemoryWallet $wallet): ?self
    {
        if ($wallet === null) {
            return null;
        }

        return new self(
            address: $wallet->address(),
            username: $wallet->username(),
            isContract: $wallet->isContract(),
        );
    }
}
