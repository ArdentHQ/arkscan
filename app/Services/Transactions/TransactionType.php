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
        return $this->isCoreTypeGroup() && $this->transaction->type === CoreTransactionTypeEnum::TRANSFER;
    }

    public function isSecondSignature(): bool
    {
        return $this->isCoreTypeGroup() && $this->transaction->type === CoreTransactionTypeEnum::SECOND_SIGNATURE;
    }

    public function isDelegateRegistration(): bool
    {
        return $this->isCoreTypeGroup() && $this->transaction->type === CoreTransactionTypeEnum::DELEGATE_REGISTRATION;
    }

    public function isVote(): bool
    {
        return $this->isCoreTypeGroup() && $this->transaction->type === CoreTransactionTypeEnum::VOTE;
    }

    public function isMultiSignature(): bool
    {
        return $this->isCoreTypeGroup() && $this->transaction->type === CoreTransactionTypeEnum::MULTI_SIGNATURE;
    }

    public function isIpfs(): bool
    {
        return $this->isCoreTypeGroup() && $this->transaction->type === CoreTransactionTypeEnum::IPFS;
    }

    public function isDelegateResignation(): bool
    {
        return $this->isCoreTypeGroup() && $this->transaction->type === CoreTransactionTypeEnum::DELEGATE_RESIGNATION;
    }

    public function isMultiPayment(): bool
    {
        return $this->isCoreTypeGroup() && $this->transaction->type === CoreTransactionTypeEnum::MULTI_PAYMENT;
    }

    public function isTimelock(): bool
    {
        return $this->isCoreTypeGroup() && $this->transaction->type === CoreTransactionTypeEnum::TIMELOCK;
    }

    public function isTimelockClaim(): bool
    {
        return $this->isCoreTypeGroup() && $this->transaction->type === CoreTransactionTypeEnum::TIMELOCK_CLAIM;
    }

    public function isTimelockRefund(): bool
    {
        return $this->isCoreTypeGroup() && $this->transaction->type === CoreTransactionTypeEnum::TIMELOCK_REFUND;
    }

    public function isEntityRegistration(): bool
    {
        return
            $this->isMagistrateTypeGroup() &
            $this->transaction->type === MagistrateTransactionTypeEnum::ENTITY &&
            $this->transaction->asset &&
            $this->transaction->asset['action'] === MagistrateTransactionEntityActionEnum::REGISTER;
    }

    public function isEntityResignation(): bool
    {
        return
            $this->isMagistrateTypeGroup() &
            $this->transaction->type === MagistrateTransactionTypeEnum::ENTITY &&
            $this->transaction->asset &&
            $this->transaction->asset['action'] === MagistrateTransactionEntityActionEnum::RESIGN;
    }

    public function isEntityUpdate(): bool
    {
        return
            $this->isMagistrateTypeGroup() &
            $this->transaction->type === MagistrateTransactionTypeEnum::ENTITY &&
            $this->transaction->asset &&
            $this->transaction->asset['action'] === MagistrateTransactionEntityActionEnum::UPDATE;
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
        return $this->isMagistrateTypeGroup() && $this->transaction->type === MagistrateTransactionTypeEnum::BUSINESS_REGISTRATION;
    }

    public function isLegacyBusinessResignation(): bool
    {
        return $this->isMagistrateTypeGroup() && $this->transaction->type === MagistrateTransactionTypeEnum::BUSINESS_RESIGNATION;
    }

    public function isLegacyBusinessUpdate(): bool
    {
        return $this->isMagistrateTypeGroup() && $this->transaction->type === MagistrateTransactionTypeEnum::BUSINESS_UPDATE;
    }

    public function isLegacyBridgechainRegistration(): bool
    {
        return $this->isMagistrateTypeGroup() && $this->transaction->type === MagistrateTransactionTypeEnum::BRIDGECHAIN_REGISTRATION;
    }

    public function isLegacyBridgechainResignation(): bool
    {
        return $this->isMagistrateTypeGroup() && $this->transaction->type === MagistrateTransactionTypeEnum::BRIDGECHAIN_RESIGNATION;
    }

    public function isLegacyBridgechainUpdate(): bool
    {
        return $this->isMagistrateTypeGroup() && $this->transaction->type === MagistrateTransactionTypeEnum::BRIDGECHAIN_UPDATE;
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
        $matchesTSubype = Arr::get($this->transaction->asset, 'subType') === $subType;

        return $matchesType && $matchesTSubype;
    }
}
