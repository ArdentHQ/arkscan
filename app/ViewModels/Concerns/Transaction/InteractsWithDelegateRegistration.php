<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use Illuminate\Support\Arr;

trait InteractsWithDelegateRegistration
{
    public function delegateUsername(): ?string
    {
        if (! $this->isDelegateRegistration()) {
            return null;
        }

        return Arr::get($this->transaction, 'asset.delegate.username');
    }

    public function blsPublicKey(): ?string
    {
        if (! $this->isBlsRegistration()) {
            return null;
        }

        return Arr::get($this->transaction, 'asset.blsPublicKey.newBlsPublicKey');
    }

    public function oldBlsPublicKey(): ?string
    {
        if (! $this->isBlsRegistration()) {
            return null;
        }

        return Arr::get($this->transaction, 'asset.blsPublicKey.oldBlsPublicKey');
    }
}
