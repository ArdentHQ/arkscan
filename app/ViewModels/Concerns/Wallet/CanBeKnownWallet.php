<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

trait CanBeKnownWallet
{
    public function hasUsername(): bool
    {
        return $this->username() !== null;
    }

    public function username(): ?string
    {
        $knownWallet = $this->findWalletByKnown();
        if (! is_null($knownWallet)) {
            return $knownWallet['name'];
        }

        return $this->wallet->username();
    }
}
