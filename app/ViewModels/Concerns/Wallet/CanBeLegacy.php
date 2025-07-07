<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

use App\Services\Addresses\Legacy;
use Illuminate\Support\Arr;

trait CanBeLegacy
{
    public function isLegacy(): bool
    {
        return Arr::get($this->wallet, 'attributes.isLegacy') === true;
    }

    public function legacyAddress(): ?string
    {
        if (! $this->isLegacy()) {
            return null;
        }

        if ($this->wallet->public_key === null) {
            return null;
        }

        return Legacy::generateAddressFromPublicKey($this->wallet->public_key);
    }
}
