<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\DTO\MemoryWallet as Base;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('IMemoryWallet')]
class MemoryWallet extends Data
{
    public function __construct(
        public string $address,
        public ?string $publicKey,
        public bool $isContract,
        public bool $hasUsername,
        public ?string $username,
        public bool $isValidator,
    ) {
    }

    public static function fromBase(Base $wallet): self
    {
        return new self(
            address: $wallet->address(),
            publicKey: $wallet->publicKey(),
            isContract: $wallet->isContract(),
            hasUsername: $wallet->hasUsername(),
            username: $wallet->username(),
            isValidator: $wallet->isValidator(),
        );
    }
}
