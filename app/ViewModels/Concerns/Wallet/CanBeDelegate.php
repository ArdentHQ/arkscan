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
        return Arr::has($this->wallet, 'attributes.delegate');
    }

    public function isResigned(): bool
    {
        return Arr::has($this->wallet, 'attributes.delegate.resigned');
    }

    public function resignationId(): ?string
    {
        if (is_null($this->wallet->public_key)) {
            return null;
        }

        if (! Arr::has($this->wallet, 'attributes.delegate.resigned')) {
            return null;
        }

        return (new WalletCache())->getResignationId($this->wallet->public_key);
    }

    public function username(): ?string
    {
        $knownWallet = $this->findWalletByKnown();

        if (! is_null($knownWallet)) {
            return $knownWallet['name'];
        }

        return Arr::get($this->wallet, 'attributes.delegate.username');
    }

    /**
     * @codeCoverageIgnore
     */
    public function rank(): ?int
    {
        return Arr::get($this->wallet, 'attributes.delegate.rank', 0);
    }

    public function delegateRankStyling(): string
    {
        if ($this->isResigned()) {
            return 'text-theme-secondary-500 border-theme-secondary-500 dark:text-theme-secondary-800 dark:border-theme-secondary-800';
        }

        if ($this->rank() > Network::delegateCount()) {
            return 'text-theme-secondary-900 border-theme-secondary-900';
        }

        return 'text-theme-secondary-900 border-theme-secondary-900';
    }

    public function delegateStatusStyling(): string
    {
        if ($this->rank() === 0 || $this->isResigned()) {
            return 'text-theme-secondary-500 border-theme-secondary-500 dark:text-theme-secondary-800 dark:border-theme-secondary-800';
        }

        if ($this->rank() > Network::delegateCount()) {
            return 'text-theme-secondary-500 border-theme-secondary-500 dark:text-theme-secondary-800 dark:border-theme-secondary-800';
        }

        return 'text-theme-success-600 border-theme-success-600';
    }
}
