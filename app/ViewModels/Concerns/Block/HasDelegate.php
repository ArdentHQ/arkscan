<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Block;

use App\DTO\MemoryWallet;

trait HasDelegate
{
    public function delegate(): MemoryWallet
    {
        return MemoryWallet::fromPublicKey($this->block->generator_public_key);
    }

    public function address(): string
    {
        return $this->delegate()->address() ?? 'Genesis';
    }

    public function username(): string
    {
        return $this->delegate()->username() ?? 'Genesis';
    }
}
