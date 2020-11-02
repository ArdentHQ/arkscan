<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Block;

use App\Facades\Wallets;
use App\Models\Wallet;
use Illuminate\Support\Arr;

trait HasDelegate
{
    public function delegate(): ?Wallet
    {
        return Wallets::findByPublicKey($this->block->generator_public_key);
    }

    public function address(): string
    {
        return Arr::get($this->delegate() ?? [], 'address', 'Genesis');
    }

    public function username(): string
    {
        return Arr::get($this->delegate() ?? [], 'attributes.delegate.username', 'Genesis');
    }
}
