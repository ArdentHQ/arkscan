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

    public function isSecondSignature(): bool
    {
        return $this->type->isSecondSignature();
    }

    public function isDelegateRegistration(): bool
    {
        return $this->type->isDelegateRegistration();
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

    public function isIpfs(): bool
    {
        return $this->type->isIpfs();
    }

    public function isDelegateResignation(): bool
    {
        return $this->type->isDelegateResignation();
    }

    public function isMultiPayment(): bool
    {
        return $this->type->isMultiPayment();
    }

    public function isTimelock(): bool
    {
        return $this->type->isTimelock();
    }

    public function isTimelockClaim(): bool
    {
        return $this->type->isTimelockClaim();
    }

    public function isTimelockRefund(): bool
    {
        return $this->type->isTimelockRefund();
    }

    public function isEntityRegistration(): bool
    {
        return $this->type->isEntityRegistration();
    }

    public function isEntityResignation(): bool
    {
        return $this->type->isEntityResignation();
    }

    public function isEntityUpdate(): bool
    {
        return $this->type->isEntityUpdate();
    }

    public function isBusinessEntityRegistration(): bool
    {
        return $this->type->isBusinessEntityRegistration();
    }

    public function isBusinessEntityResignation(): bool
    {
        return $this->type->isBusinessEntityResignation();
    }

    public function isBusinessEntityUpdate(): bool
    {
        return $this->type->isBusinessEntityUpdate();
    }

    public function isProductEntityRegistration(): bool
    {
        return $this->type->isProductEntityRegistration();
    }

    public function isProductEntityResignation(): bool
    {
        return $this->type->isProductEntityResignation();
    }

    public function isProductEntityUpdate(): bool
    {
        return $this->type->isProductEntityUpdate();
    }

    public function isPluginEntityRegistration(): bool
    {
        return $this->type->isPluginEntityRegistration();
    }

    public function isPluginEntityResignation(): bool
    {
        return $this->type->isPluginEntityResignation();
    }

    public function isPluginEntityUpdate(): bool
    {
        return $this->type->isPluginEntityUpdate();
    }

    public function isModuleEntityRegistration(): bool
    {
        return $this->type->isModuleEntityRegistration();
    }

    public function isModuleEntityResignation(): bool
    {
        return $this->type->isModuleEntityResignation();
    }

    public function isModuleEntityUpdate(): bool
    {
        return $this->type->isModuleEntityUpdate();
    }

    public function isDelegateEntityRegistration(): bool
    {
        return $this->type->isDelegateEntityRegistration();
    }

    public function isDelegateEntityResignation(): bool
    {
        return $this->type->isDelegateEntityResignation();
    }

    public function isDelegateEntityUpdate(): bool
    {
        return $this->type->isDelegateEntityUpdate();
    }

    public function isLegacyBusinessRegistration(): bool
    {
        return $this->type->isLegacyBusinessRegistration();
    }

    public function isLegacyBusinessResignation(): bool
    {
        return $this->type->isLegacyBusinessResignation();
    }

    public function isLegacyBusinessUpdate(): bool
    {
        return $this->type->isLegacyBusinessUpdate();
    }

    public function isLegacyBridgechainRegistration(): bool
    {
        return $this->type->isLegacyBridgechainRegistration();
    }

    public function isLegacyBridgechainResignation(): bool
    {
        return $this->type->isLegacyBridgechainResignation();
    }

    public function isLegacyBridgechainUpdate(): bool
    {
        return $this->type->isLegacyBridgechainUpdate();
    }

    public function isUnknown(): bool
    {
        return $this->type->isUnknown();
    }

    public function isSelfReceiving(): bool
    {
        if ($this->isDelegateRegistration()) {
            return true;
        }

        if ($this->isDelegateResignation()) {
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

        if ($this->isSecondSignature()) {
            return true;
        }

        if ($this->isEntityRegistration()) {
            return true;
        }

        if ($this->isEntityResignation()) {
            return true;
        }

        if ($this->isEntityUpdate()) {
            return true;
        }

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
}
