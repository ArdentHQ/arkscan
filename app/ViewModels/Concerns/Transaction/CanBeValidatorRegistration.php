<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

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
