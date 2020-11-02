<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

use App\Services\Cache\WalletCache;
use Illuminate\Support\Arr;

trait CanBeDelegate
{
    public function isDelegate(): bool
    {
        return Arr::has($this->wallet, 'attributes.delegate');
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
        return Arr::get($this->wallet, 'attributes.delegate.username');
    }

    /**
     * @codeCoverageIgnore
     */
    public function rank(): ?int
    {
        return Arr::get($this->wallet, 'attributes.delegate.rank', 0);
    }
}
