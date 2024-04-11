<?php

declare(strict_types=1);

namespace App\Services\Transactions;

use App\Enums\TransactionTypeEnum;
use App\Enums\MagistrateTransactionEntityActionEnum;
use App\Enums\MagistrateTransactionEntityTypeEnum;
use App\Enums\MagistrateTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Models\Transaction;
use App\ViewModels\Concerns\Transaction\InteractsWithVendorField;
use Illuminate\Support\Arr;

final class TransactionType
{
    use InteractsWithVendorField;

    private array $types = [
        'isTransfer'                       => 'transfer',
        'isSecondSignature'                => 'second-signature',
        'isValidatorRegistration'          => 'validator-registration',
        'isUsernameRegistration'           => 'username-registration',
        'isUsernameResignation'            => 'username-resignation',
        'isVoteCombination'                => 'vote-combination',
        'isUnvote'                         => 'unvote',
        'isVote'                           => 'vote',
        'isMultiSignature'                 => 'multi-signature',
        'isIpfs'                           => 'ipfs',
        'isValidatorResignation'           => 'validator-resignation',
        'isMultiPayment'                   => 'multi-payment',
        'isBusinessEntityRegistration'     => 'business-entity-registration',
        'isBusinessEntityResignation'      => 'business-entity-resignation',
        'isBusinessEntityUpdate'           => 'business-entity-update',
        'isProductEntityRegistration'      => 'product-entity-registration',
        'isProductEntityResignation'       => 'product-entity-resignation',
        'isProductEntityUpdate'            => 'product-entity-update',
        'isPluginEntityRegistration'       => 'plugin-entity-registration',
        'isPluginEntityResignation'        => 'plugin-entity-resignation',
        'isPluginEntityUpdate'             => 'plugin-entity-update',
        'isModuleEntityRegistration'       => 'module-entity-registration',
        'isModuleEntityResignation'        => 'module-entity-resignation',
        'isModuleEntityUpdate'             => 'module-entity-update',
        'isValidatorEntityRegistration'    => 'validator-entity-registration',
        'isValidatorEntityResignation'     => 'validator-entity-resignation',
        'isValidatorEntityUpdate'          => 'validator-entity-update',
        'isLegacyBusinessRegistration'     => 'legacy-business-registration',
        'isLegacyBusinessResignation'      => 'legacy-business-resignation',
        'isLegacyBusinessUpdate'           => 'legacy-business-update',
        'isLegacyBridgechainRegistration'  => 'bridgechain-entity-registration',
        'isLegacyBridgechainResignation'   => 'bridgechain-entity-resignation',
        'isLegacyBridgechainUpdate'        => 'bridgechain-entity-update',
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
        return $this->isCoreType(TransactionTypeEnum::TRANSFER);
    }

    public function isValidatorRegistration(): bool
    {
        return $this->isCoreType(TransactionTypeEnum::VALIDATOR_REGISTRATION);
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

    public function isValidatorResignation(): bool
    {
        return $this->isCoreType(CoreTransactionTypeEnum::VALIDATOR_RESIGNATION);
    }

    public function isMultiPayment(): bool
    {
        return $this->isCoreType(CoreTransactionTypeEnum::MULTI_PAYMENT);
    }

    public function isUsernameRegistration(): bool
    {
        return $this->isCoreType(CoreTransactionTypeEnum::USERNAME_REGISTRATION);
    }

    public function isUsernameResignation(): bool
    {
        return $this->isCoreType(CoreTransactionTypeEnum::USERNAME_RESIGNATION);
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

    public function isValidatorEntityRegistration(): bool
    {
        return $this->isEntityWithRegistration(MagistrateTransactionEntityTypeEnum::VALIDATOR);
    }

    public function isValidatorEntityResignation(): bool
    {
        return $this->isEntityWithResignation(MagistrateTransactionEntityTypeEnum::VALIDATOR);
    }

    public function isValidatorEntityUpdate(): bool
    {
        return $this->isEntityWithUpdate(MagistrateTransactionEntityTypeEnum::VALIDATOR);
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

        if ($this->isValidatorRegistration()) {
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

        if ($this->isValidatorResignation()) {
            return false;
        }

        if ($this->isMultiPayment()) {
            return false;
        }

        if ($this->isUsernameRegistration()) {
            return false;
        }

        if ($this->isUsernameResignation()) {
            return false;
        }

        return true;
    }

    private function determineVoteTypes(): array
    {
        $containsVote   = false;
        $containsUnvote = false;

        if ($this->transaction->type !== TransactionTypeEnum::VOTE) {
            return [$containsVote, $containsUnvote];
        }

        if (! is_array($this->transaction->asset)) {
            return [$containsVote, $containsUnvote];
        }

        $containsVote   = count(Arr::get($this->transaction->asset, 'votes', [])) !== 0;
        $containsUnvote = count(Arr::get($this->transaction->asset, 'unvotes', [])) !== 0;

        return [$containsVote, $containsUnvote];
    }
}
