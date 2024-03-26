<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use Illuminate\Support\Arr;

trait InteractsWithValidatorRegistration
{
    public function validatorUsername(): ?string
    {
        if (! $this->isValidatorRegistration()) {
            return null;
        }

        return Arr::get($this->transaction, 'asset.username');
    }
}
