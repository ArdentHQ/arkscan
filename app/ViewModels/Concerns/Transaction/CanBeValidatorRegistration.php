<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\Models\Scopes\ValidatorRegistrationScope;
use App\Models\Scopes\ValidatorResignationScope;
use App\Models\Transaction;
use App\ViewModels\TransactionViewModel;

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

    public function validatorRegistration(): ?TransactionViewModel
    {
        if (! $this->isValidatorResignation()) {
            return null;
        }

        /** @var ?Transaction $transaction */
        $transaction = Transaction::where('sender_public_key', $this->transaction->sender_public_key)
            ->withScope(ValidatorRegistrationScope::class)
            ->first();

        if ($transaction === null) {
            return null;
        }

        return new TransactionViewModel($transaction);
    }
}
