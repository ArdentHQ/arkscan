<?php

declare(strict_types=1);

namespace App\Services\Transactions;

use App\Enums\CoreTransactionTypeEnum;
use App\Enums\MagistrateTransactionEntityActionEnum;
use App\Enums\MagistrateTransactionEntitySubTypeEnum;
use App\Enums\MagistrateTransactionEntityTypeEnum;
use App\Enums\MagistrateTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Models\Transaction;
use Illuminate\Support\Arr;

final class TransactionType
{
    private Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function isTransfer(): bool
    {
        if (! $this->isCoreTypeGroup()) {
            return false;
        }

        return $this->transaction->type === CoreTransactionTypeEnum::TRANSFER;
    }

    public function isSecondSignature(): bool
    {
        if (! $this->isCoreTypeGroup()) {
            return false;
        }

        return $this->transaction->type === CoreTransactionTypeEnum::SECOND_SIGNATURE;
    }

    public function isDelegateRegistration(): bool
    {
        if (! $this->isCoreTypeGroup()) {
            return false;
        }

        return $this->transaction->type === CoreTransactionTypeEnum::DELEGATE_REGISTRATION;
    }

    public function isVote(): bool
    {
        if (! $this->isCoreTypeGroup()) {
            return false;
        }

        return $this->transaction->type === CoreTransactionTypeEnum::VOTE;
    }

    public function isMultiSignature(): bool
    {
        if (! $this->isCoreTypeGroup()) {
            return false;
        }

        return $this->transaction->type === CoreTransactionTypeEnum::MULTI_SIGNATURE;
    }

    public function isIpfs(): bool
    {
        if (! $this->isCoreTypeGroup()) {
            return false;
        }

        return $this->transaction->type === CoreTransactionTypeEnum::IPFS;
    }

    public function isDelegateResignation(): bool
    {
        if (! $this->isCoreTypeGroup()) {
            return false;
        }

        return $this->transaction->type === CoreTransactionTypeEnum::DELEGATE_RESIGNATION;
    }

    public function isMultiPayment(): bool
    {
        if (! $this->isCoreTypeGroup()) {
            return false;
        }

        return $this->transaction->type === CoreTransactionTypeEnum::MULTI_PAYMENT;
    }

    public function isTimelock(): bool
    {
        if (! $this->isCoreTypeGroup()) {
            return false;
        }

        return $this->transaction->type === CoreTransactionTypeEnum::TIMELOCK;
    }

    public function isTimelockClaim(): bool
    {
        if (! $this->isCoreTypeGroup()) {
            return false;
        }

        return $this->transaction->type === CoreTransactionTypeEnum::TIMELOCK_CLAIM;
    }

    public function isTimelockRefund(): bool
    {
        if (! $this->isCoreTypeGroup()) {
            return false;
        }

        return $this->transaction->type === CoreTransactionTypeEnum::TIMELOCK_REFUND;
    }

    public function isEntityRegistration(): bool
    {
        if (! $this->isMagistrateTypeGroup()) {
            return false;
        }

        if (is_null($this->transaction->asset)) {
            return false;
        }

        return $this->isTypeWithAction(MagistrateTransactionTypeEnum::ENTITY, MagistrateTransactionEntityActionEnum::REGISTER);
    }

    public function isEntityResignation(): bool
    {
        if (! $this->isMagistrateTypeGroup()) {
            return false;
        }

        if (is_null($this->transaction->asset)) {
            return false;
        }

        return $this->isTypeWithAction(MagistrateTransactionTypeEnum::ENTITY, MagistrateTransactionEntityActionEnum::RESIGN);
    }

    public function isEntityUpdate(): bool
    {
        if (! $this->isMagistrateTypeGroup()) {
            return false;
        }

        if (is_null($this->transaction->asset)) {
            return false;
        }

        return $this->isTypeWithAction(MagistrateTransactionTypeEnum::ENTITY, MagistrateTransactionEntityActionEnum::UPDATE);
    }

    public function isBusinessEntityRegistration(): bool
    {
        if (! $this->isEntityRegistration()) {
            return false;
        }

        return $this->isTypeWithSubType(MagistrateTransactionEntityTypeEnum::BUSINESS, MagistrateTransactionEntitySubTypeEnum::NONE);
    }

    public function isBusinessEntityResignation(): bool
    {
        if (! $this->isEntityResignation()) {
            return false;
        }

        return $this->isTypeWithSubType(MagistrateTransactionEntityTypeEnum::BUSINESS, MagistrateTransactionEntitySubTypeEnum::NONE);
    }

    public function isBusinessEntityUpdate(): bool
    {
        if (! $this->isEntityUpdate()) {
            return false;
        }

        return $this->isTypeWithSubType(MagistrateTransactionEntityTypeEnum::BUSINESS, MagistrateTransactionEntitySubTypeEnum::NONE);
    }

    public function isProductEntityRegistration(): bool
    {
        if (! $this->isEntityRegistration()) {
            return false;
        }

        return $this->isTypeWithSubType(MagistrateTransactionEntityTypeEnum::PRODUCT, MagistrateTransactionEntitySubTypeEnum::NONE);
    }

    public function isProductEntityResignation(): bool
    {
        if (! $this->isEntityResignation()) {
            return false;
        }

        return $this->isTypeWithSubType(MagistrateTransactionEntityTypeEnum::PRODUCT, MagistrateTransactionEntitySubTypeEnum::NONE);
    }

    public function isProductEntityUpdate(): bool
    {
        if (! $this->isEntityUpdate()) {
            return false;
        }

        return $this->isTypeWithSubType(MagistrateTransactionEntityTypeEnum::PRODUCT, MagistrateTransactionEntitySubTypeEnum::NONE);
    }

    public function isPluginEntityRegistration(): bool
    {
        if (! $this->isEntityRegistration()) {
            return false;
        }

        return $this->isTypeWithSubType(MagistrateTransactionEntityTypeEnum::PLUGIN, MagistrateTransactionEntitySubTypeEnum::NONE);
    }

    public function isPluginEntityResignation(): bool
    {
        if (! $this->isEntityResignation()) {
            return false;
        }

        return $this->isTypeWithSubType(MagistrateTransactionEntityTypeEnum::PLUGIN, MagistrateTransactionEntitySubTypeEnum::NONE);
    }

    public function isPluginEntityUpdate(): bool
    {
        if (! $this->isEntityUpdate()) {
            return false;
        }

        return $this->isTypeWithSubType(MagistrateTransactionEntityTypeEnum::PLUGIN, MagistrateTransactionEntitySubTypeEnum::NONE);
    }

    public function isModuleEntityRegistration(): bool
    {
        if (! $this->isEntityRegistration()) {
            return false;
        }

        return $this->isTypeWithSubType(MagistrateTransactionEntityTypeEnum::MODULE, MagistrateTransactionEntitySubTypeEnum::NONE);
    }

    public function isModuleEntityResignation(): bool
    {
        if (! $this->isEntityResignation()) {
            return false;
        }

        return $this->isTypeWithSubType(MagistrateTransactionEntityTypeEnum::MODULE, MagistrateTransactionEntitySubTypeEnum::NONE);
    }

    public function isModuleEntityUpdate(): bool
    {
        if (! $this->isEntityUpdate()) {
            return false;
        }

        return $this->isTypeWithSubType(MagistrateTransactionEntityTypeEnum::MODULE, MagistrateTransactionEntitySubTypeEnum::NONE);
    }

    public function isDelegateEntityRegistration(): bool
    {
        if (! $this->isEntityRegistration()) {
            return false;
        }

        return $this->isTypeWithSubType(MagistrateTransactionEntityTypeEnum::DELEGATE, MagistrateTransactionEntitySubTypeEnum::NONE);
    }

    public function isDelegateEntityResignation(): bool
    {
        if (! $this->isEntityResignation()) {
            return false;
        }

        return $this->isTypeWithSubType(MagistrateTransactionEntityTypeEnum::DELEGATE, MagistrateTransactionEntitySubTypeEnum::NONE);
    }

    public function isDelegateEntityUpdate(): bool
    {
        if (! $this->isEntityUpdate()) {
            return false;
        }

        return $this->isTypeWithSubType(MagistrateTransactionEntityTypeEnum::DELEGATE, MagistrateTransactionEntitySubTypeEnum::NONE);
    }

    public function isLegacyBusinessRegistration(): bool
    {
        if (! $this->isMagistrateTypeGroup()) {
            return false;
        }

        return $this->transaction->type === MagistrateTransactionTypeEnum::BUSINESS_REGISTRATION;
    }

    public function isLegacyBusinessResignation(): bool
    {
        if (! $this->isMagistrateTypeGroup()) {
            return false;
        }

        return $this->transaction->type === MagistrateTransactionTypeEnum::BUSINESS_RESIGNATION;
    }

    public function isLegacyBusinessUpdate(): bool
    {
        if (! $this->isMagistrateTypeGroup()) {
            return false;
        }

        return $this->transaction->type === MagistrateTransactionTypeEnum::BUSINESS_UPDATE;
    }

    public function isLegacyBridgechainRegistration(): bool
    {
        if (! $this->isMagistrateTypeGroup()) {
            return false;
        }

        return $this->transaction->type === MagistrateTransactionTypeEnum::BRIDGECHAIN_REGISTRATION;
    }

    public function isLegacyBridgechainResignation(): bool
    {
        if (! $this->isMagistrateTypeGroup()) {
            return false;
        }

        return $this->transaction->type === MagistrateTransactionTypeEnum::BRIDGECHAIN_RESIGNATION;
    }

    public function isLegacyBridgechainUpdate(): bool
    {
        if (! $this->isMagistrateTypeGroup()) {
            return false;
        }

        return $this->transaction->type === MagistrateTransactionTypeEnum::BRIDGECHAIN_UPDATE;
    }

    private function isCoreTypeGroup(): bool
    {
        return $this->transaction->type_group === TransactionTypeGroupEnum::CORE;
    }

    private function isMagistrateTypeGroup(): bool
    {
        return $this->transaction->type_group === TransactionTypeGroupEnum::MAGISTRATE;
    }

    private function isTypeWithSubType(int $type, int $subType): bool
    {
        $matchesType    = Arr::get($this->transaction->asset, 'type') === $type;
        $matchesSubType = Arr::get($this->transaction->asset, 'subType') === $subType;

        return $matchesType && $matchesSubType;
    }

    private function isTypeWithAction(int $type, int $action): bool
    {
        $matchesType   = Arr::get($this->transaction->asset, 'type') === $type;
        $matchesAction = Arr::get($this->transaction->asset, 'action') === $action;

        return $matchesType && $matchesAction;
    }
}
