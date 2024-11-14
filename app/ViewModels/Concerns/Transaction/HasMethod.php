<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\Services\Transactions\TransactionMethod;

trait HasMethod
{
    public function typeName(): string
    {
        return (new TransactionMethod($this->transaction))->name();
    }

    public function isTransfer(): bool
    {
        return $this->method->isTransfer();
    }

    public function isValidatorRegistration(): bool
    {
        return $this->method->isValidatorRegistration();
    }

    public function isVote(): bool
    {
        return $this->method->isVote();
    }

    public function isUnvote(): bool
    {
        return $this->method->isUnvote();
    }

    public function isValidatorResignation(): bool
    {
        return $this->method->isValidatorResignation();
    }

    public function isUnknown(): bool
    {
        return $this->method->isUnknown();
    }

    public function isSelfReceiving(): bool
    {
        if ($this->isValidatorRegistration()) {
            return true;
        }

        if ($this->isValidatorResignation()) {
            return true;
        }

        if ($this->isVote()) {
            return true;
        }

        if ($this->isUnvote()) {
            return true;
        }

        return false;
    }
}
