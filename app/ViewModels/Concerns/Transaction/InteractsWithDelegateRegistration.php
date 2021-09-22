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
}
