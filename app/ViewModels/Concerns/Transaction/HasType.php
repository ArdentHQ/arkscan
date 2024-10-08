<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\Services\Transactions\TransactionType;

trait HasType
{
    public function typeName(): string
    {
        return (new TransactionType($this->transaction))->name();
    }

    public function isTransfer(): bool
    {
        return $this->type->isTransfer();
    }

    public function isValidatorRegistration(): bool
    {
        return $this->type->isValidatorRegistration();
    }

    public function isVote(): bool
    {
        return $this->type->isVote();
    }

    public function isUnvote(): bool
    {
        return $this->type->isUnvote();
    }

    public function isVoteCombination(): bool
    {
        return $this->type->isVoteCombination();
    }

    public function isMultiSignature(): bool
    {
        return $this->type->isMultiSignature();
    }

    public function isValidatorResignation(): bool
    {
        return $this->type->isValidatorResignation();
    }

    public function isMultiPayment(): bool
    {
        return $this->type->isMultiPayment();
    }

    public function isUsernameRegistration(): bool
    {
        return $this->type->isUsernameRegistration();
    }

    public function isUsernameResignation(): bool
    {
        return $this->type->isUsernameResignation();
    }

    // TODO: implement method correctly - https://app.clickup.com/t/86dur8fj6
    public function isEvm(): bool
    {
        return true;
    }

    public function isLegacy(): bool
    {
        if ($this->isValidatorRegistration()) {
            return false;
        }

        if ($this->isValidatorResignation()) {
            return false;
        }

        if ($this->isUsernameRegistration()) {
            return false;
        }

        if ($this->isUsernameResignation()) {
            return false;
        }

        if ($this->isMultiPayment()) {
            return false;
        }

        if ($this->isVoteCombination()) {
            return false;
        }

        if ($this->isVote()) {
            return false;
        }

        if ($this->isUnvote()) {
            return false;
        }

        if ($this->isTransfer()) {
            return false;
        }

        if ($this->isMultiSignature()) {
            return false;
        }

        return true;
    }

    public function isUnknown(): bool
    {
        return $this->type->isUnknown();
    }

    public function isSelfReceiving(): bool
    {
        if ($this->isValidatorRegistration()) {
            return true;
        }

        if ($this->isValidatorResignation()) {
            return true;
        }

        if ($this->isVoteCombination()) {
            return true;
        }

        if ($this->isVote()) {
            return true;
        }

        if ($this->isUnvote()) {
            return true;
        }

        if ($this->isUsernameRegistration()) {
            return true;
        }

        if ($this->isUsernameResignation()) {
            return true;
        }

        return false;
    }
}
