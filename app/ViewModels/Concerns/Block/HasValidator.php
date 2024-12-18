<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Block;

use App\DTO\MemoryWallet;

trait HasValidator
{
    public function validator(): MemoryWallet
    {
        return MemoryWallet::fromAddress($this->block->generator_address);
    }

    public function address(): string
    {
        return $this->validator()->address() ?? 'Genesis';
    }

    public function username(): string
    {
        return $this->validator()->username() ?? 'Genesis';
    }
}
