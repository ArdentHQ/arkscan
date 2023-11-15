<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

use App\Facades\Network;
use App\Services\Cache\WalletCache;
use Illuminate\Support\Arr;

trait CanBeDelegate
{
    public function isDelegate(): bool
    {
        return Arr::has($this->wallet, 'attributes.validatorRank');
    }

    public function isResigned(): bool
    {
        return Arr::has($this->wallet, 'attributes.validatorResigned');
    }

    public function isStandby(): bool
    {
        return $this->rank() > Network::delegateCount();
    }

    public function isActive(): bool
    {
        if (! $this->isDelegate()) {
            return false;
        }

        if ($this->isResigned()) {
            return false;
        }

        return ! $this->isStandby();
    }

    public function resignationId(): ?string
    {
        if (is_null($this->wallet->public_key)) {
            return null;
        }

        if (! Arr::has($this->wallet, 'attributes.validatorResigned')) {
            return null;
        }

        return (new WalletCache())->getResignationId($this->wallet->public_key);
    }

    public function delegateUsername(): ?string
    {
        return $this->wallet->username();
    }

    public function username(): ?string
    {
        $knownWallet = $this->findWalletByKnown();

        if (! is_null($knownWallet)) {
            return $knownWallet['name'];
        }

        return $this->delegateUsername();
    }

    public function rank(): ?int
    {
        return Arr::get($this->wallet, 'attributes.validatorRank', 0);
    }
}
