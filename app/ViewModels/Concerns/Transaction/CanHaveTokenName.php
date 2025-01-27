<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\Services\Cache\TokenTransferCache;

trait CanHaveTokenName
{
    public function tokenName(): ?string
    {
        return (new TokenTransferCache())->getTokenName($this->transaction->id);
    }
}
