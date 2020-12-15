<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\Services\Transactions\TransactionTypeComponent;

trait InteractsWithTypeData
{
    public function typeLabel(): string
    {
        if ($this->isLegacyType()) {
            return trans('general.transaction.types.'.$this->typeName());
        }

        return trans('general.transaction.types.'.$this->iconType());
    }

    public function headerComponent(): string
    {
        return (new TransactionTypeComponent($this->transaction))->header();
    }

    public function typeComponent(): string
    {
        return (new TransactionTypeComponent($this->transaction))->details();
    }

    public function extensionComponent(): string
    {
        return (new TransactionTypeComponent($this->transaction))->extension();
    }

    public function isLegacyType(): bool
    {
        if ($this->isLegacyBusinessRegistration()) {
            return true;
        }

        if ($this->isLegacyBusinessResignation()) {
            return true;
        }

        if ($this->isLegacyBusinessUpdate()) {
            return true;
        }

        if ($this->isLegacyBridgechainRegistration()) {
            return true;
        }

        if ($this->isLegacyBridgechainResignation()) {
            return true;
        }

        if ($this->isLegacyBridgechainUpdate()) {
            return true;
        }

        return false;
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
