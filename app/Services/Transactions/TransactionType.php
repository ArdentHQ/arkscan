<?php

declare(strict_types=1);

namespace App\Services\Transactions;

use App\Enums\CoreTransactionTypeEnum;
use App\Enums\MagistrateTransactionEntityActionEnum;
use App\Enums\MagistrateTransactionEntityTypeEnum;
use App\Enums\MagistrateTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Models\Transaction;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class TransactionType
{
    private Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function isTransfer(): bool
    {
        return $this->isCoreType(CoreTransactionTypeEnum::TRANSFER);
    }

    public function isSecondSignature(): bool
    {
        return $this->isCoreType(CoreTransactionTypeEnum::SECOND_SIGNATURE);
    }

    public function isDelegateRegistration(): bool
    {
        return $this->isCoreType(CoreTransactionTypeEnum::DELEGATE_REGISTRATION);
    }

    public function isVote(): bool
    {
        return $this->determineVoteTypes()[0] === true;
    }

    public function isUnvote(): bool
    {
        return $this->determineVoteTypes()[1] === true;
    }

    public function isVoteCombination(): bool
    {
        [$containsVote, $containsUnvote] = $this->determineVoteTypes();

        return $containsVote && $containsUnvote;
    }

    public function isMultiSignature(): bool
    {
        return $this->isCoreType(CoreTransactionTypeEnum::MULTI_SIGNATURE);
    }

    public function isIpfs(): bool
    {
        return $this->isCoreType(CoreTransactionTypeEnum::IPFS);
    }

    public function isDelegateResignation(): bool
    {
        return $this->isCoreType(CoreTransactionTypeEnum::DELEGATE_RESIGNATION);
    }

    public function isMultiPayment(): bool
    {
        return $this->isCoreType(CoreTransactionTypeEnum::MULTI_PAYMENT);
    }

    public function isTimelock(): bool
    {
        return $this->isCoreType(CoreTransactionTypeEnum::TIMELOCK);
    }

    public function isTimelockClaim(): bool
    {
        return $this->isCoreType(CoreTransactionTypeEnum::TIMELOCK_CLAIM);
    }

    public function isTimelockRefund(): bool
    {
        return $this->isCoreType(CoreTransactionTypeEnum::TIMELOCK_REFUND);
    }

    public function isEntityRegistration(): bool
    {
        return $this->isEntityAction(MagistrateTransactionEntityActionEnum::REGISTER);
    }

    public function isEntityResignation(): bool
    {
        return $this->isEntityAction(MagistrateTransactionEntityActionEnum::RESIGN);
    }

    public function isEntityUpdate(): bool
    {
        return $this->isEntityAction(MagistrateTransactionEntityActionEnum::UPDATE);
    }

    public function isBusinessEntityRegistration(): bool
    {
        return $this->isEntityWithRegistration(MagistrateTransactionEntityTypeEnum::BUSINESS);
    }

    public function isBusinessEntityResignation(): bool
    {
        return $this->isEntityWithResignation(MagistrateTransactionEntityTypeEnum::BUSINESS);
    }

    public function isBusinessEntityUpdate(): bool
    {
        return $this->isEntityWithUpdate(MagistrateTransactionEntityTypeEnum::BUSINESS);
    }

    public function isProductEntityRegistration(): bool
    {
        return $this->isEntityWithRegistration(MagistrateTransactionEntityTypeEnum::PRODUCT);
    }

    public function isProductEntityResignation(): bool
    {
        return $this->isEntityWithResignation(MagistrateTransactionEntityTypeEnum::PRODUCT);
    }

    public function isProductEntityUpdate(): bool
    {
        return $this->isEntityWithUpdate(MagistrateTransactionEntityTypeEnum::PRODUCT);
    }

    public function isPluginEntityRegistration(): bool
    {
        return $this->isEntityWithRegistration(MagistrateTransactionEntityTypeEnum::PLUGIN);
    }

    public function isPluginEntityResignation(): bool
    {
        return $this->isEntityWithResignation(MagistrateTransactionEntityTypeEnum::PLUGIN);
    }

    public function isPluginEntityUpdate(): bool
    {
        return $this->isEntityWithUpdate(MagistrateTransactionEntityTypeEnum::PLUGIN);
    }

    public function isModuleEntityRegistration(): bool
    {
        return $this->isEntityWithRegistration(MagistrateTransactionEntityTypeEnum::MODULE);
    }

    public function isModuleEntityResignation(): bool
    {
        return $this->isEntityWithResignation(MagistrateTransactionEntityTypeEnum::MODULE);
    }

    public function isModuleEntityUpdate(): bool
    {
        return $this->isEntityWithUpdate(MagistrateTransactionEntityTypeEnum::MODULE);
    }

    public function isDelegateEntityRegistration(): bool
    {
        return $this->isEntityWithRegistration(MagistrateTransactionEntityTypeEnum::DELEGATE);
    }

    public function isDelegateEntityResignation(): bool
    {
        return $this->isEntityWithResignation(MagistrateTransactionEntityTypeEnum::DELEGATE);
    }

    public function isDelegateEntityUpdate(): bool
    {
        return $this->isEntityWithUpdate(MagistrateTransactionEntityTypeEnum::DELEGATE);
    }

    public function isLegacyBusinessRegistration(): bool
    {
        return $this->isMagistrateType(MagistrateTransactionTypeEnum::BUSINESS_REGISTRATION);
    }

    public function isLegacyBusinessResignation(): bool
    {
        return $this->isMagistrateType(MagistrateTransactionTypeEnum::BUSINESS_RESIGNATION);
    }

    public function isLegacyBusinessUpdate(): bool
    {
        return $this->isMagistrateType(MagistrateTransactionTypeEnum::BUSINESS_UPDATE);
    }

    public function isLegacyBridgechainRegistration(): bool
    {
        return $this->isMagistrateType(MagistrateTransactionTypeEnum::BRIDGECHAIN_REGISTRATION);
    }

    public function isLegacyBridgechainResignation(): bool
    {
        return $this->isMagistrateType(MagistrateTransactionTypeEnum::BRIDGECHAIN_RESIGNATION);
    }

    public function isLegacyBridgechainUpdate(): bool
    {
        return $this->isMagistrateType(MagistrateTransactionTypeEnum::BRIDGECHAIN_UPDATE);
    }

    private function isCoreType(int $type): bool
    {
        $matchesType      = $this->transaction->type === $type;
        $matchesTypeGroup = $this->transaction->type_group === TransactionTypeGroupEnum::CORE;

        return $matchesType && $matchesTypeGroup;
    }

    private function isMagistrateType(int $type): bool
    {
        $matchesType      = $this->transaction->type === $type;
        $matchesTypeGroup = $this->transaction->type_group === TransactionTypeGroupEnum::MAGISTRATE;

        return $matchesType && $matchesTypeGroup;
    }

    private function isEntityWithRegistration(int $type): bool
    {
        if (! $this->isEntityRegistration()) {
            return false;
        }

        return $this->isEntityType($type);
    }

    private function isEntityWithResignation(int $type): bool
    {
        if (! $this->isEntityResignation()) {
            return false;
        }

        return $this->isEntityType($type);
    }

    private function isEntityWithUpdate(int $type): bool
    {
        if (! $this->isEntityUpdate()) {
            return false;
        }

        return $this->isEntityType($type);
    }

    private function isEntityType(int $type): bool
    {
        return Arr::get($this->transaction->asset ?? [], 'type') === $type;
    }

    private function isEntityAction(int $action): bool
    {
        if (! $this->isMagistrateTypeGroup()) {
            return false;
        }

        $matchesType   = $this->transaction->type === MagistrateTransactionTypeEnum::ENTITY;
        $matchesAction = Arr::get($this->transaction->asset ?? [], 'action') === $action;

        return $matchesType && $matchesAction;
    }

    private function isMagistrateTypeGroup(): bool
    {
        return $this->transaction->type_group === TransactionTypeGroupEnum::MAGISTRATE;
    }

    private function determineVoteTypes(): array
    {
        $containsVote   = false;
        $containsUnvote = false;

        if (! $this->isCoreType(CoreTransactionTypeEnum::VOTE)) {
            return [$containsVote, $containsUnvote];
        }

        if (! is_array($this->transaction->asset)) {
            return [$containsVote, $containsUnvote];
        }

        if (! Arr::has($this->transaction->asset, 'votes')) {
            return [$containsVote, $containsUnvote];
        }

        foreach ($this->transaction->asset['votes'] as $vote) {
            if (Str::startsWith($vote, '+')) {
                $containsVote = true;
            }

            if (Str::startsWith($vote, '-')) {
                $containsUnvote = true;
            }
        }

        return [$containsVote, $containsUnvote];
    }
}
