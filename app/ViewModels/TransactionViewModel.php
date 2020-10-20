<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Facades\Network;
use App\Models\Transaction;
use App\Services\Blockchain\NetworkStatus;
use App\Services\NumberFormatter;
use App\Services\Transactions\TransactionDirection;
use App\Services\Transactions\TransactionDirectionIcon;
use App\Services\Transactions\TransactionState;
use App\Services\Transactions\TransactionStateIcon;
use App\Services\Transactions\TransactionType;
use App\Services\Transactions\TransactionTypeIcon;
use ARKEcosystem\UserInterface\Support\DateFormat;
use Illuminate\Support\Carbon;
use Spatie\ViewModels\ViewModel;

final class TransactionViewModel extends ViewModel
{
    private Transaction $model;

    private TransactionType $type;

    private TransactionState $state;

    private TransactionDirection $direction;

    public function __construct(Transaction $transaction)
    {
        $this->model     = $transaction;
        $this->type      = new TransactionType($transaction);
        $this->state     = new TransactionState($transaction);
        $this->direction = new TransactionDirection($transaction);
    }

    public function url(): string
    {
        return route('transaction', $this->model->id);
    }

    public function id(): string
    {
        return $this->model->id;
    }

    public function blockId(): string
    {
        return $this->model->block_id;
    }

    public function timestamp(): string
    {
        return Carbon::parse(\ArkEcosystem\Crypto\Configuration\Network::get()->epoch())
            ->addSeconds($this->model->timestamp)
            ->format(DateFormat::TIME);
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

    public function iconState(): string
    {
        return (new TransactionStateIcon($this->model))->name();
    }

    public function iconType(): string
    {
        return (new TransactionTypeIcon($this->model))->name();
    }

    public function iconDirection(string $address): string
    {
        return (new TransactionDirectionIcon($this->model))->name($address);
    }

    public function isConfirmed(): bool
    {
        return $this->state->isConfirmed();
    }

    public function isSent(string $address): bool
    {
        return $this->direction->isSent($address);
    }

    public function isReceived(string $address): bool
    {
        return $this->direction->isReceived($address);
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
}
