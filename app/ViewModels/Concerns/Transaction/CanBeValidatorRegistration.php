<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\Facades\Network;
use App\Services\Cache\WalletCache;
use Illuminate\Support\Arr;

trait CanBeValidatorRegistration
{
    public function validatorPublicKey(): ?string
    {
        if (! $this->isValidatorRegistration()) {
            return null;
        }

        [2 => $arguments] = $this->getMethodData();

        return $arguments[0];
    }
}
