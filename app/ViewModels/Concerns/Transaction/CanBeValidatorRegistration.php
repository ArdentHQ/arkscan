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

        $methodData = $this->getMethodData();
        if ($methodData === null) {
            return null;
        }

        [2 => $arguments] = $methodData;

        return $arguments[0];
    }
}
