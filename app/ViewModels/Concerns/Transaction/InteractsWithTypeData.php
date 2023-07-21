<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use Illuminate\Contracts\Translation\Translator;

trait InteractsWithTypeData
{
    public function typeLabel(): string
    {
        if (app(Translator::class)->has('pages.transaction.types.'.$this->typeName())) {
            return trans('pages.transaction.types.'.$this->typeName());
        }

        return trans('general.transaction.types.'.$this->iconType());
    }

    public function hasExtraData(): bool
    {
        if ($this->isMultiSignature()) {
            return true;
        }

        if ($this->isVoteCombination()) {
            return true;
        }

        if ($this->isMultiPayment()) {
            return true;
        }

        return false;
    }

    /**
     * The transactions that return `false` are the ones that don't show an
     * amount on the transaction details page. We are validating one by one so
     * it can be considered within the test coverage driver.
     */
    public function hasAmount(): bool
    {
        if ($this->isDelegateRegistration()) {
            return false;
        }

        if ($this->isEntityRegistration()) {
            return false;
        }

        if ($this->isEntityResignation()) {
            return false;
        }

        if ($this->isEntityUpdate()) {
            return false;
        }

        if ($this->isMultiSignature()) {
            return false;
        }

        if ($this->isVoteCombination()) {
            return false;
        }

        if ($this->isUnvote()) {
            return false;
        }

        if ($this->isVote()) {
            return false;
        }

        return true;
    }

    public function isRegistration(): bool
    {
        if ($this->isDelegateRegistration()) {
            return true;
        }

        if ($this->isEntityRegistration()) {
            return true;
        }

        return false;
    }
}
