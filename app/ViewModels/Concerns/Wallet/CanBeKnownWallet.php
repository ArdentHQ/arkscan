<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

trait CanBeKnownWallet
{
    public function isKnownWallet(): bool
    {
        return ! is_null($this->findWalletByKnown());
    }

    public function walletName(): ?string
    {
        $knownWallet = $this->findWalletByKnown();
        if (! is_null($knownWallet)) {
            return $knownWallet['name'];
        }

        return null;
    }
}
