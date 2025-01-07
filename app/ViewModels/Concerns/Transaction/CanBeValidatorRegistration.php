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
        // @codeCoverageIgnoreStart
        // Not covered in tests, since having a null value depends on returning
        // null on the rawPayload method which I was not able to mock
        if ($methodData === null) {
            return null;
        }
        // @codeCoverageIgnoreEnd

        [2 => $arguments] = $methodData;

        return $arguments[0];
    }
}
