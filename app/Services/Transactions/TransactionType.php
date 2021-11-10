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
    private array $types = [
        'isTransfer'                      => 'transfer',
        'isSecondSignature'               => 'second-signature',
        'isDelegateRegistration'          => 'delegate-registration',
        'isVoteCombination'               => 'vote-combination',
        'isUnvote'                        => 'unvote',
        'isVote'                          => 'vote',
        'isMultiSignature'                => 'multi-signature',
        'isIpfs'                          => 'ipfs',
        'isDelegateResignation'           => 'delegate-resignation',
        'isMultiPayment'                  => 'multi-payment',
        'isTimelock'                      => 'timelock',
        'isTimelockClaim'                 => 'timelock-claim',
        'isTimelockRefund'                => 'timelock-refund',
        'isBusinessEntityRegistration'    => 'business-entity-registration',
        'isBusinessEntityResignation'     => 'business-entity-resignation',
        'isBusinessEntityUpdate'          => 'business-entity-update',
        'isProductEntityRegistration'     => 'product-entity-registration',
        'isProductEntityResignation'      => 'product-entity-resignation',
        'isProductEntityUpdate'           => 'product-entity-update',
        'isPluginEntityRegistration'      => 'plugin-entity-registration',
        'isPluginEntityResignation'       => 'plugin-entity-resignation',
        'isPluginEntityUpdate'            => 'plugin-entity-update',
        'isModuleEntityRegistration'      => 'module-entity-registration',
        'isModuleEntityResignation'       => 'module-entity-resignation',
        'isModuleEntityUpdate'            => 'module-entity-update',
        'isDelegateEntityRegistration'    => 'delegate-entity-registration',
        'isDelegateEntityResignation'     => 'delegate-entity-resignation',
        'isDelegateEntityUpdate'          => 'delegate-entity-update',
        'isLegacyBusinessRegistration'    => 'legacy-business-registration',
        'isLegacyBusinessResignation'     => 'legacy-business-resignation',
        'isLegacyBusinessUpdate'          => 'legacy-business-update',
        'isLegacyBridgechainRegistration' => 'bridgechain-entity-registration',
        'isLegacyBridgechainResignation'  => 'bridgechain-entity-resignation',
        'isLegacyBridgechainUpdate'       => 'bridgechain-entity-update',
    ];

    public function __construct(private Transaction $transaction)
    {
    }

    public function name(): string
    {
        foreach ($this->types as $method => $name) {
            if ((bool) call_user_func_safe([$this, $method])) {
                return $name;
            }
        }

        return 'unknown';
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

    public function isUnknown(): bool
    {
        if ($this->isTransfer()) {
            return false;
        }

        if ($this->isSecondSignature()) {
            return false;
        }

        if ($this->isDelegateRegistration()) {
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

        if ($this->isMultiSignature()) {
            return false;
        }

        if ($this->isIpfs()) {
            return false;
        }

        if ($this->isDelegateResignation()) {
            return false;
        }

        if ($this->isMultiPayment()) {
            return false;
        }

        if ($this->isTimelock()) {
            return false;
        }

        if ($this->isTimelockClaim()) {
            return false;
        }

        if ($this->isTimelockRefund()) {
            return false;
        }

        if ($this->isBusinessEntityRegistration()) {
            return false;
        }

        if ($this->isBusinessEntityResignation()) {
            return false;
        }

        if ($this->isBusinessEntityUpdate()) {
            return false;
        }

        if ($this->isProductEntityRegistration()) {
            return false;
        }

        if ($this->isProductEntityResignation()) {
            return false;
        }

        if ($this->isProductEntityUpdate()) {
            return false;
        }

        if ($this->isPluginEntityRegistration()) {
            return false;
        }

        if ($this->isPluginEntityResignation()) {
            return false;
        }

        if ($this->isPluginEntityUpdate()) {
            return false;
        }

        if ($this->isModuleEntityRegistration()) {
            return false;
        }

        if ($this->isModuleEntityResignation()) {
            return false;
        }

        if ($this->isModuleEntityUpdate()) {
            return false;
        }

        if ($this->isDelegateEntityRegistration()) {
            return false;
        }

        if ($this->isDelegateEntityResignation()) {
            return false;
        }

        if ($this->isDelegateEntityUpdate()) {
            return false;
        }

        if ($this->isLegacyBusinessRegistration()) {
            return false;
        }

        if ($this->isLegacyBusinessResignation()) {
            return false;
        }

        if ($this->isLegacyBusinessUpdate()) {
            return false;
        }

        if ($this->isLegacyBridgechainRegistration()) {
            return false;
        }

        if ($this->isLegacyBridgechainResignation()) {
            return false;
        }

        if ($this->isLegacyBridgechainUpdate()) {
            return false;
        }

        return true;
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
