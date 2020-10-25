<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Facades\Network;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Blockchain\NetworkStatus;
use App\Services\ExchangeRate;
use App\Services\MultiSignature;
use App\Services\NumberFormatter;
use App\Services\Timestamp;
use App\Services\Transactions\TransactionDirection;
use App\Services\Transactions\TransactionDirectionIcon;
use App\Services\Transactions\TransactionState;
use App\Services\Transactions\TransactionStateIcon;
use App\Services\Transactions\TransactionType;
use App\Services\Transactions\TransactionTypeIcon;
use ArkEcosystem\Crypto\Identities\Address;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Spatie\ViewModels\ViewModel;

final class TransactionViewModel extends ViewModel
{
    private Transaction $transaction;

    private TransactionType $type;

    private TransactionState $state;

    private TransactionDirection $direction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
        $this->type        = new TransactionType($transaction);
        $this->state       = new TransactionState($transaction);
        $this->direction   = new TransactionDirection($transaction);
    }

    public function url(): string
    {
        return route('transaction', $this->transaction);
    }

    public function id(): string
    {
        return $this->transaction->id;
    }

    public function blockId(): string
    {
        return $this->transaction->block_id;
    }

    public function timestamp(): string
    {
        return Timestamp::fromGenesisHuman($this->transaction->timestamp);
    }

    public function nonce(): string
    {
        $wallet = Cache::remember(
            "transaction:wallet:{$this->transaction->sender_public_key}",
            Carbon::now()->addHour(),
            fn () => $this->transaction->sender
        );

        return NumberFormatter::number($wallet->nonce);
    }

    public function sender(): string
    {
        $wallet = Cache::remember(
            "transaction:wallet:{$this->transaction->sender_public_key}",
            Carbon::now()->addHour(),
            fn () => $this->transaction->sender
        );

        if (is_null($wallet)) {
            return 'n/a';
        }

        return $wallet->address;
    }

    public function recipient(): string
    {
        $wallet = Cache::remember(
            "transaction:wallet:{$this->transaction->recipient_id}",
            Carbon::now()->addHour(),
            fn () => $this->transaction->recipient
        );

        if (is_null($wallet)) {
            return 'n/a';
        }

        return $wallet->address;
    }

    public function multiSignatureAddress(): ?string
    {
        if (! $this->isMultiSignature()) {
            return null;
        }

        if (is_null($this->transaction->asset)) {
            return null;
        }

        return MultiSignature::address(
            Arr::get($this->transaction->asset, 'multiSignature.min', 0),
            Arr::get($this->transaction->asset, 'multiSignature.publicKeys', [])
        );
    }

    public function payments(): array
    {
        if (! $this->isMultiPayment()) {
            return [];
        }

        if (is_null($this->transaction->asset)) {
            return [];
        }

        return collect(Arr::get($this->transaction->asset, 'payments', []))
            ->map(fn ($payment) => [
                'amount'      => NumberFormatter::currency($payment['amount'], Network::currency()),
                'recipientId' => $payment['recipientId'],
            ])
            ->toArray();
    }

    public function recipientsCount(): string
    {
        if (! $this->isMultiPayment()) {
            return NumberFormatter::number(0);
        }

        if (is_null($this->transaction->asset)) {
            return NumberFormatter::number(0);
        }

        return NumberFormatter::number(count(Arr::get($this->transaction->asset, 'payments')));
    }

    public function participants(): array
    {
        if (! $this->isMultiSignature()) {
            return [];
        }

        if (is_null($this->transaction->asset)) {
            return [];
        }

        return collect(Arr::get($this->transaction->asset, 'multiSignature.publicKeys', []))
            ->map(fn ($publicKey) => Address::fromPublicKey($publicKey))
            ->toArray();
    }

    public function fee(): string
    {
        return NumberFormatter::currency($this->transaction->fee / 1e8, Network::currency());
    }

    public function feeFiat(): string
    {
        return ExchangeRate::convert($this->transaction->fee / 1e8, $this->transaction->timestamp);
    }

    public function amount(): string
    {
        return NumberFormatter::currency($this->transaction->amount / 1e8, Network::currency());
    }

    public function amountFiat(): string
    {
        return ExchangeRate::convert($this->transaction->amount / 1e8, $this->transaction->timestamp);
    }

    /**
     * @codeCoverageIgnore
     */
    public function vendorField(): ?string
    {
        /* @phpstan-ignore-next-line */
        $vendorFieldHex = $this->transaction->vendor_field_hex;

        if (is_null($vendorFieldHex)) {
            return null;
        }

        $vendorFieldStream = stream_get_contents($vendorFieldHex);

        if ($vendorFieldStream === false) {
            return null;
        }

        $vendorField = hex2bin(bin2hex($vendorFieldStream));

        if ($vendorField === false) {
            return null;
        }

        return $vendorField;
    }

    public function confirmations(): string
    {
        $block = Cache::remember(
            "transaction:confirmations:{$this->transaction->block_id}",
            Carbon::now()->addHour(),
            fn () => $this->transaction->block
        );

        if (is_null($block)) {
            return NumberFormatter::number(0);
        }

        return NumberFormatter::number(NetworkStatus::height() - $block->height);
    }

    public function voted(): ?Wallet
    {
        if (! $this->isVote()) {
            return null;
        }

        $publicKey = collect(Arr::get($this->transaction->asset ?? [], 'votes'))
            ->filter(fn ($vote) => Str::startsWith($vote, '+'))
            ->first();

        return Cache::remember(
            "transaction:wallet:{$publicKey}",
            Carbon::now()->addHour(),
            fn () => Wallet::where('public_key', substr($publicKey, 1))->firstOrFail()
        );
    }

    public function unvoted(): ?Wallet
    {
        if (! $this->isUnvote()) {
            return null;
        }

        $publicKey = collect(Arr::get($this->transaction->asset ?? [], 'votes'))
            ->filter(fn ($vote) => Str::startsWith($vote, '-'))
            ->first();

        return Cache::remember(
            "transaction:wallet:{$publicKey}",
            Carbon::now()->addHour(),
            fn () => Wallet::where('public_key', substr($publicKey, 1))->firstOrFail()
        );
    }

    public function iconState(): string
    {
        return (new TransactionStateIcon($this->transaction))->name();
    }

    public function iconType(): string
    {
        return (new TransactionTypeIcon($this->transaction))->name();
    }

    public function iconDirection(string $address): string
    {
        return (new TransactionDirectionIcon($this->transaction))->name($address);
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

    public function typeLabel(): string
    {
        return trans('general.transaction.'.$this->iconType());
    }

    public function typeComponent(): string
    {
        $view = 'transaction.details.'.Str::slug($this->iconType());

        if (View::exists("components.$view")) {
            return $view;
        }

        return 'transaction.details.fallback';
    }

    public function extraComponent(): string
    {
        return 'transaction.extra.'.trim(Str::slug($this->iconType()));
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
}
