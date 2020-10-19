<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Enums\CoreTransactionTypeEnum;
use App\Enums\MagistrateTransactionEntityActionEnum;
use App\Enums\MagistrateTransactionEntitySubTypeEnum;
use App\Enums\MagistrateTransactionEntityTypeEnum;
use App\Enums\MagistrateTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Facades\Network;
use App\Models\Transaction;
use App\Services\Blockchain\NetworkStatus;
use App\Services\NumberFormatter;
use ARKEcosystem\UserInterface\Support\DateFormat;
use Illuminate\Support\Carbon;
use Spatie\ViewModels\ViewModel;

final class TransactionViewModel extends ViewModel
{
    private Transaction $model;

    public function __construct(Transaction $transaction)
    {
        $this->model = $transaction;
    }

    public function id(): string
    {
        return $this->model->id;
    }

    public function timestamp(): string
    {
        return Carbon::parse(\ArkEcosystem\Crypto\Configuration\Network::get()->epoch())
            ->addSeconds($this->model->timestamp)
            ->format(DateFormat::TIME);
    }

    public function type(): string
    {
        return $this->model->type;
    }

    public function sender(): string
    {
        return $this->model->sender->address;
    }

    public function recipient(): string
    {
        return $this->model->recipient->address;
    }

    public function fee(): string
    {
        return NumberFormatter::currency($this->model->fee / 1e8, Network::currency());
    }

    public function amount(): string
    {
        return NumberFormatter::currency($this->model->amount / 1e8, Network::currency());
    }

    public function confirmations(): string
    {
        return NumberFormatter::number(NetworkStatus::height() - $this->model->block->height);
    }

    public function isTransfer(): bool
    {
        return $this->isCoreTypeGroup() && $this->model->type === CoreTransactionTypeEnum::TRANSFER;
    }

    public function isSecondSignature(): bool
    {
        return $this->isCoreTypeGroup() && $this->model->type === CoreTransactionTypeEnum::SECOND_SIGNATURE;
    }

    public function isDelegateRegistration(): bool
    {
        return $this->isCoreTypeGroup() && $this->model->type === CoreTransactionTypeEnum::DELEGATE_REGISTRATION;
    }

    public function isVote(): bool
    {
        return $this->isCoreTypeGroup() && $this->model->type === CoreTransactionTypeEnum::VOTE;
    }

    public function isMultiSignature(): bool
    {
        return $this->isCoreTypeGroup() && $this->model->type === CoreTransactionTypeEnum::MULTI_SIGNATURE;
    }

    public function isIpfs(): bool
    {
        return $this->isCoreTypeGroup() && $this->model->type === CoreTransactionTypeEnum::IPFS;
    }

    public function isDelegateResignation(): bool
    {
        return $this->isCoreTypeGroup() && $this->model->type === CoreTransactionTypeEnum::DELEGATE_RESIGNATION;
    }

    public function isMultiPayment(): bool
    {
        return $this->isCoreTypeGroup() && $this->model->type === CoreTransactionTypeEnum::MULTI_PAYMENT;
    }

    public function isTimelock(): bool
    {
        return $this->isCoreTypeGroup() && $this->model->type === CoreTransactionTypeEnum::TIMELOCK;
    }

    public function isTimelockClaim(): bool
    {
        return $this->isCoreTypeGroup() && $this->model->type === CoreTransactionTypeEnum::TIMELOCK_CLAIM;
    }

    public function isTimelockRefund(): bool
    {
        return $this->isCoreTypeGroup() && $this->model->type === CoreTransactionTypeEnum::TIMELOCK_REFUND;
    }

    public function isEntityRegistration(): bool
    {
        return
            $this->isMagistrateTypeGroup() &
            $this->model->type === MagistrateTransactionTypeEnum::ENTITY &&
            $this->model->asset &&
            $this->model->asset['action'] === MagistrateTransactionEntityActionEnum::REGISTER;
    }

    public function isEntityResignation(): bool
    {
        return
            $this->isMagistrateTypeGroup() &
            $this->model->type === MagistrateTransactionTypeEnum::ENTITY &&
            $this->model->asset &&
            $this->model->asset['action'] === MagistrateTransactionEntityActionEnum::RESIGN;
    }

    public function isEntityUpdate(): bool
    {
        return
            $this->isMagistrateTypeGroup() &
            $this->model->type === MagistrateTransactionTypeEnum::ENTITY &&
            $this->model->asset &&
            $this->model->asset['action'] === MagistrateTransactionEntityActionEnum::UPDATE;
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

        return $this->isTypeWithSubType(MagistrateTransactionEntityTypeEnum::PLUGIN, MagistrateTransactionEntitySubTypeEnum::NONE);
    }

    public function isModuleEntityResignation(): bool
    {
        if (! $this->isEntityResignation()) {
            return false;
        }

        return $this->isTypeWithSubType(MagistrateTransactionEntityTypeEnum::PLUGIN, MagistrateTransactionEntitySubTypeEnum::NONE);
    }

    public function isModuleEntityUpdate(): bool
    {
        if (! $this->isEntityUpdate()) {
            return false;
        }

        return $this->isTypeWithSubType(MagistrateTransactionEntityTypeEnum::PLUGIN, MagistrateTransactionEntitySubTypeEnum::NONE);
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
        return $this->isMagistrateTypeGroup() && $this->model->type === MagistrateTransactionTypeEnum::BUSINESS_REGISTRATION;
    }

    public function isLegacyBusinessResignation(): bool
    {
        return $this->isMagistrateTypeGroup() && $this->model->type === MagistrateTransactionTypeEnum::BUSINESS_RESIGNATION;
    }

    public function isLegacyBusinessUpdate(): bool
    {
        return $this->isMagistrateTypeGroup() && $this->model->type === MagistrateTransactionTypeEnum::BUSINESS_UPDATE;
    }

    public function isLegacyBridgechainRegistration(): bool
    {
        return $this->isMagistrateTypeGroup() && $this->model->type === MagistrateTransactionTypeEnum::BRIDGECHAIN_REGISTRATION;
    }

    public function isLegacyBridgechainResignation(): bool
    {
        return $this->isMagistrateTypeGroup() && $this->model->type === MagistrateTransactionTypeEnum::BRIDGECHAIN_RESIGNATION;
    }

    public function isLegacyBridgechainUpdate(): bool
    {
        return $this->isMagistrateTypeGroup() && $this->model->type === MagistrateTransactionTypeEnum::BRIDGECHAIN_UPDATE;
    }

    private function isCoreTypeGroup(): bool
    {
        return $this->model->type_group === TransactionTypeGroupEnum::CORE;
    }

    private function isMagistrateTypeGroup(): bool
    {
        return $this->model->type_group === TransactionTypeGroupEnum::MAGISTRATE;
    }

    private function isTypeWithSubType(int $type, int $subType): bool
    {
        $matchesType    = $this->model->asset['type'] === $type;
        $matchesTSubype = $this->model->asset['subType'] === $subType;

        return $matchesType && $matchesTSubype;
    }
}
