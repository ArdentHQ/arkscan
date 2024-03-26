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

    public function isIpfs(): bool
    {
        return $this->type->isIpfs();
    }

    public function isValidatorResignation(): bool
    {
        return $this->type->isValidatorResignation();
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

    public function isUsernameRegistration(): bool
    {
        return $this->type->isUsernameRegistration();
    }

    public function isUsernameResignation(): bool
    {
        return $this->type->isUsernameResignation();
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

    public function isValidatorEntityRegistration(): bool
    {
        return $this->type->isValidatorEntityRegistration();
    }

    public function isValidatorEntityResignation(): bool
    {
        return $this->type->isValidatorEntityResignation();
    }

    public function isValidatorEntityUpdate(): bool
    {
        return $this->type->isValidatorEntityUpdate();
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

        if ($this->isIpfs()) {
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

        if ($this->isSecondSignature()) {
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
