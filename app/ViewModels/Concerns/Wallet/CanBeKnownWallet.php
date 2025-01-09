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
        return $this->wallet->username();
    }

    public function usernameIfNotKnown(): ?string
    {
        $knownWallet = $this->findWalletByKnown();
        if (! is_null($knownWallet)) {
            return $knownWallet['name'];
        }

        return $this->username();
    }

    public function usernameBeforeKnown(): ?string
    {
        $username = $this->username();
        if ($username !== null) {
            return $username;
        }

        $knownWallet = $this->findWalletByKnown();
        if (! is_null($knownWallet)) {
            return $knownWallet['name'];
        }

        return null;
    }
}
