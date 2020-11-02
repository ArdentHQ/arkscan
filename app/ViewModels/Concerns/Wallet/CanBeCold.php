<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

trait CanBeCold
{
    public function isCold(): bool
    {
        return is_null($this->publicKey());
    }
}
