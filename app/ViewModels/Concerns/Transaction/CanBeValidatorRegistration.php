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

        /** @var array $methodData */
        $methodData = $this->getMethodData();

        [2 => $arguments] = $methodData;

        return $arguments[0];
    }
}
