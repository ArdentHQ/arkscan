<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\Actions\CacheNetworkHeight;
use App\Contracts\ViewModel;
use App\DTO\Inertia\Wallet as WalletDTO;
use App\DTO\MemoryWallet;
use App\Facades\Wallets;
use App\Models\MultiPayment;
use App\Models\Scopes\ValidatorRegistrationScope;
use App\Models\Transaction as Model;
use App\Services\BigNumber;
use App\Services\ExchangeRate;
use App\Services\Identity;
use App\Services\Timestamp;
use App\Services\Transactions\TransactionMethod;
use App\Services\Transactions\TransactionState;
use App\ViewModels\WalletViewModel;
use ArkEcosystem\Crypto\Utils\Abi\ArgumentDecoder;
use ArkEcosystem\Crypto\Utils\UnitConverter;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use RuntimeException;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('ITransaction')]
class Transaction extends Data implements ViewModel
{
    private ?Model $model = null;

    /** @var array<int, mixed>|null */
    private ?array $methodArguments = null;

    public function __construct(
        public string $hash,
        public string $block_hash,
        public int $block_number,
        public int $transaction_index,
        public int $timestamp,
        public int $nonce,
        public string $sender_public_key,
        public string $from,
        public ?string $to,
        public string $value,
        public string $gas_price,
        public string $gas,
        public bool $status,
        public string $gas_used,
        public string $gas_refunded,
        public ?string $deployed_contract_address,
        public ?string $decoded_error,
        #[LiteralTypeScriptType('string[]')]
        public array $multi_payment_recipients,
        public float $amount,
        public float $amountForItself,
        public float $amountExcludingItself,
        public float $amountWithFee,
        public float $amountReceived,
        public int | string $amountFiat,
        public int | string $amountReceivedFiat,
        public float $fee,
        public int | string $feeFiat,
        public string $type,
        public string $url,
        public bool $isTransfer,
        public bool $isTokenTransfer,
        public bool $isVote,
        public bool $isUnvote,
        public bool $isValidatorRegistration,
        public bool $isValidatorResignation,
        public bool $isValidatorUpdate,
        public bool $isUsernameRegistration,
        public bool $isUsernameResignation,
        public bool $isContractDeployment,
        public bool $isMultiPayment,
        public bool $isSelfReceiving,
        public bool $isSent,
        public bool $isSentToSelf,
        public bool $isReceived,
        public bool $hasFailedStatus,
        public ?self $validatorRegistration,
        public ?string $votedFor,
        public ?string $votedForUsername,
        public ?WalletDTO $sender,
        public ?WalletDTO $recipient,
    ) {
    }

    public static function fromModel(Model $transaction, ?string $address = null): self
    {
        $method          = new TransactionMethod($transaction);
        $methodArguments = $method->arguments();

        $isTransfer              = $method->isTransfer();
        $isTokenTransfer         = $method->isTokenTransfer();
        $isVote                  = $method->isVote();
        $isUnvote                = $method->isUnvote();
        $isValidatorRegistration = $method->isValidatorRegistration();
        $isValidatorResignation  = $method->isValidatorResignation();
        $isValidatorUpdate       = $method->isValidatorUpdate();
        $isUsernameRegistration  = $method->isUsernameRegistration();
        $isUsernameResignation   = $method->isUsernameResignation();
        $isContractDeployment    = $method->isContractDeployment();
        $isMultiPayment          = $method->isMultiPayment();
        $isSelfReceiving         = self::calculateIsSelfReceiving(
            $isValidatorRegistration,
            $isValidatorResignation,
            $isValidatorUpdate,
            $isVote,
            $isUnvote,
        );

        $amount              = self::calculateAmount($transaction, $isMultiPayment);
        $amountForItself     = self::calculateAmountForItself($transaction, $isMultiPayment);
        $amountExcludingSelf = self::calculateAmountExcludingItself($transaction, $isMultiPayment);
        $fee                 = self::calculateFee($transaction);
        $amountWithFee       = self::calculateAmountWithFee($transaction, $fee);

        $address = $address ?? $transaction->from;

        $amountReceived = self::calculateAmountReceived($transaction, $isMultiPayment, $address, $amount);
        $isSent         = self::calculateIsSent($transaction, $address);
        $isSentToSelf   = self::calculateIsSentToSelf($transaction, $method, $address);
        $isReceived     = self::calculateIsReceived($transaction, $address);

        $votedFor         = null;
        $votedForUsername = null;
        if ($isVote) {
            $votedWallet = $transaction->votedFor;
            if ($votedWallet !== null) {
                $votedForUsername = $votedWallet->username();
                $votedFor         = $votedWallet->address;
            }
        }

        $sender    = self::resolveSenderWallet($transaction);
        $recipient = self::resolveRecipientWallet($transaction, $isTransfer, $isTokenTransfer);

        $validatorRegistration = self::resolveValidatorRegistration($transaction, $isValidatorResignation);

        $dto = new self(
            hash: $transaction->hash,
            block_hash: $transaction->block_hash,
            block_number: $transaction->block_number,
            transaction_index: $transaction->transaction_index,
            timestamp: $transaction->timestamp,
            nonce: $transaction->nonce,
            sender_public_key: $transaction->sender_public_key,
            from: $transaction->from,
            to: $transaction->to,
            value: (string) $transaction->value,
            gas_price: (string) $transaction->gas_price,
            gas: (string) $transaction->gas,
            status: $transaction->status,
            gas_used: (string) $transaction->gas_used,
            gas_refunded: (string) $transaction->gas_refunded,
            deployed_contract_address: $transaction->deployed_contract_address,
            decoded_error: $transaction->decoded_error,
            multi_payment_recipients: $transaction->multi_payment_recipients,
            amount: $amount,
            amountForItself: $amountForItself,
            amountExcludingItself: $amountExcludingSelf,
            amountWithFee: $amountWithFee,
            amountReceived: $amountReceived,
            amountFiat: self::calculateAmountFiat($amount, $transaction->timestamp),
            amountReceivedFiat: self::calculateAmountReceivedFiat($amountReceived, $transaction->timestamp),
            fee: $fee,
            feeFiat: self::calculateFeeFiat($fee, $transaction->timestamp),
            type: $method->name(),
            url: route('transaction', $transaction->hash),
            isTransfer: $isTransfer,
            isTokenTransfer: $isTokenTransfer,
            isVote: $isVote,
            isUnvote: $isUnvote,
            isValidatorRegistration: $isValidatorRegistration,
            isValidatorResignation: $isValidatorResignation,
            isValidatorUpdate: $isValidatorUpdate,
            isUsernameRegistration: $isUsernameRegistration,
            isUsernameResignation: $isUsernameResignation,
            isContractDeployment: $isContractDeployment,
            isMultiPayment: $isMultiPayment,
            isSelfReceiving: $isSelfReceiving,
            isSent: $isSent,
            isSentToSelf: $isSentToSelf,
            isReceived: $isReceived,
            hasFailedStatus: $transaction->status === false,
            validatorRegistration: $validatorRegistration,
            votedFor: $votedFor,
            votedForUsername: $votedForUsername,
            sender: $sender,
            recipient: $recipient,
        );

        $dto->setModel($transaction, $methodArguments);

        return $dto;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function model(): Model
    {
        if ($this->model === null) {
            throw new RuntimeException('Transaction model is not available on this DTO instance.');
        }

        return $this->model;
    }

    public function hash(): string
    {
        return $this->hash;
    }

    public function blockHash(): string
    {
        return $this->block_hash;
    }

    public function blockHeight(): int
    {
        return $this->block_number;
    }

    public function timestamp(): string
    {
        return Timestamp::fromUnixHuman($this->timestamp);
    }

    public function dateTime(): Carbon
    {
        return Timestamp::fromUnix($this->timestamp);
    }

    public function nonce(): int
    {
        return $this->nonce;
    }

    public function gas(): float
    {
        return UnitConverter::formatUnits($this->gas, 'wei');
    }

    public function gasUsed(): float
    {
        return UnitConverter::formatUnits($this->gas_used, 'wei');
    }

    public function transactionIndex(): int
    {
        return $this->transaction_index;
    }

    public function fee(): float
    {
        return $this->fee;
    }

    public function feeFiat(bool $showSmallAmounts = false): string
    {
        return ExchangeRate::convert($this->fee(), $this->timestamp, $showSmallAmounts);
    }

    public function amount(?string $walletAddress = null): float
    {
        return $this->amount;
    }

    public function amountForItself(): float
    {
        return $this->amountForItself;
    }

    public function amountExcludingItself(): float
    {
        return $this->amountExcludingItself;
    }

    public function amountWithFee(): float
    {
        return $this->amountWithFee;
    }

    public function amountReceived(?string $walletAddress = null): float
    {
        if ($walletAddress === null) {
            return $this->amountReceived;
        }

        if ($this->model === null) {
            return $this->amountReceived;
        }

        return self::calculateAmountReceived($this->model, $this->isMultiPayment, $walletAddress, $this->amount);
    }

    public function amountFiatExcludingItself(): string
    {
        return ExchangeRate::convert($this->amountExcludingItself(), $this->timestamp);
    }

    public function amountFiat(bool $showSmallAmounts = false): string
    {
        return ExchangeRate::convert($this->amount(), $this->timestamp, $showSmallAmounts);
    }

    public function amountReceivedFiat(?string $walletAddress = null): string
    {
        return ExchangeRate::convert($this->amountReceived($walletAddress), $this->timestamp);
    }

    public function totalFiat(bool $withSmallAmounts = false): string
    {
        return ExchangeRate::convert($this->amountWithFee(), $this->timestamp, $withSmallAmounts);
    }

    public function confirmations(): int
    {
        return abs(CacheNetworkHeight::execute() - $this->block_number);
    }

    public function typeName(): string
    {
        return $this->type;
    }

    public function isTransfer(): bool
    {
        return $this->isTransfer;
    }

    public function isTokenTransfer(): bool
    {
        return $this->isTokenTransfer;
    }

    public function isVote(): bool
    {
        return $this->isVote;
    }

    public function isUnvote(): bool
    {
        return $this->isUnvote;
    }

    public function isValidatorRegistration(): bool
    {
        return $this->isValidatorRegistration;
    }

    public function isValidatorResignation(): bool
    {
        return $this->isValidatorResignation;
    }

    public function isValidatorUpdate(): bool
    {
        return $this->isValidatorUpdate;
    }

    public function isUsernameRegistration(): bool
    {
        return $this->isUsernameRegistration;
    }

    public function isUsernameResignation(): bool
    {
        return $this->isUsernameResignation;
    }

    public function isContractDeployment(): bool
    {
        return $this->isContractDeployment;
    }

    public function isMultiPayment(): bool
    {
        return $this->isMultiPayment;
    }

    public function isSelfReceiving(): bool
    {
        return $this->isSelfReceiving;
    }

    public function methodArguments(): array
    {
        return $this->resolveMethodArguments();
    }

    public function isSent(string $address): bool
    {
        return Identity::address($this->sender_public_key) === $address;
    }

    public function isSentToSelf(string $address): bool
    {
        if (! $this->isTransfer() && ! $this->isTokenTransfer()) {
            return false;
        }

        $sender = $this->sender();
        if ($sender !== null && $address !== $sender->address) {
            return false;
        }

        if (! $this->isMultiPayment()) {
            $recipient = $this->recipient();
            if ($recipient !== null && $address !== $recipient->address) {
                return false;
            }
        }

        return true;
    }

    public function isReceived(string $address): bool
    {
        return $this->to === $address;
    }

    public function hasPayload(): bool
    {
        return $this->model?->hasPayload() ?? false;
    }

    public function rawPayload(): ?string
    {
        return $this->model?->rawPayload();
    }

    public function utf8Payload(): ?string
    {
        return $this->model?->utf8Payload();
    }

    public function formattedPayload(): ?string
    {
        return $this->model?->formattedPayload();
    }

    /**
     * @return Collection<int, MultiPayment>
     */
    public function multiPaymentRecipients(): Collection
    {
        return $this->model?->multiPaymentRecipients ?? collect();
    }

    public function isConfirmed(): bool
    {
        if ($this->model === null) {
            return false;
        }

        return (new TransactionState($this->model))->isConfirmed();
    }

    public function hasFailedStatus(): bool
    {
        return $this->status === false;
    }

    public function transactionError(): ?string
    {
        return $this->model?->transactionError();
    }

    public function voted(): ?WalletViewModel
    {
        if ($this->model?->votedFor === null) {
            return null;
        }

        return new WalletViewModel($this->model->votedFor);
    }

    public function unvoted(): ?WalletViewModel
    {
        return $this->voted();
    }

    public function sender(): ?MemoryWallet
    {
        if ($this->sender_public_key === '') {
            return null;
        }

        return MemoryWallet::fromPublicKey($this->sender_public_key);
    }

    public function recipient(): ?MemoryWallet
    {
        if (is_null($this->to)) {
            if ($this->deployed_contract_address !== null) {
                return MemoryWallet::fromAddress($this->deployed_contract_address);
            }

            return $this->sender();
        }

        return MemoryWallet::fromAddress($this->to);
    }

    public function validatorPublicKey(): ?string
    {
        if (! $this->isValidatorRegistration() && ! $this->isValidatorUpdate()) {
            return null;
        }

        $arguments = $this->resolveMethodArguments();
        if (count($arguments) === 0) {
            return null;
        }

        return $arguments[0] ?? null;
    }

    public function validatorRegistration(): ?self
    {
        if ($this->validatorRegistration !== null) {
            return $this->validatorRegistration;
        }

        if (! $this->isValidatorResignation() || $this->model === null) {
            return null;
        }

        /** @var ?Model $transaction */
        $transaction = Model::where('sender_public_key', $this->model->sender_public_key)
            ->withScope(ValidatorRegistrationScope::class)
            ->first();

        if ($transaction === null) {
            return null;
        }

        return self::fromModel($transaction);
    }

    public function username(): ?string
    {
        $methodArguments = $this->resolveMethodArguments();
        if (count($methodArguments) === 0) {
            return null;
        }

        return (new ArgumentDecoder(implode($methodArguments)))->decodeString();
    }

    private function setModel(Model $transaction, array $methodArguments): void
    {
        $this->model           = $transaction;
        $this->methodArguments = $methodArguments;
    }

    private function resolveMethodArguments(): array
    {
        if ($this->methodArguments !== null) {
            return $this->methodArguments;
        }

        if ($this->model === null) {
            return [];
        }

        $this->methodArguments = (new TransactionMethod($this->model))->arguments();

        return $this->methodArguments;
    }

    private static function calculateIsSelfReceiving(
        bool $isValidatorRegistration,
        bool $isValidatorResignation,
        bool $isValidatorUpdate,
        bool $isVote,
        bool $isUnvote,
    ): bool {
        if ($isValidatorRegistration) {
            return true;
        }

        if ($isValidatorResignation) {
            return true;
        }

        if ($isValidatorUpdate) {
            return true;
        }

        if ($isVote) {
            return true;
        }

        if ($isUnvote) {
            return true;
        }

        return false;
    }

    private static function calculateAmount(Model $transaction, bool $isMultiPayment): float
    {
        if (! $isMultiPayment) {
            return UnitConverter::formatUnits((string) $transaction->value, 'ark');
        }

        $amount = BigNumber::zero();
        foreach ($transaction->multiPaymentRecipients as $recipient) {
            $amount->plus((string) $recipient->amount);
        }

        return $amount->toFloat();
    }

    private static function calculateAmountForItself(Model $transaction, bool $isMultiPayment): float
    {
        if (! $isMultiPayment) {
            return 0.0;
        }

        $senderAddress = Identity::address($transaction->sender_public_key);

        $amount = BigNumber::zero();
        foreach ($transaction->multiPaymentRecipients as $recipient) {
            if (strtolower($senderAddress) === strtolower($recipient['to'])) {
                $amount->plus((string) $recipient->amount);
            }
        }

        return $amount->toFloat();
    }

    private static function calculateAmountExcludingItself(Model $transaction, bool $isMultiPayment): float
    {
        if (! $isMultiPayment) {
            return 0.0;
        }

        $senderAddress = Identity::address($transaction->sender_public_key);

        $amount = BigNumber::zero();
        foreach ($transaction->multiPaymentRecipients as $recipient) {
            if (strtolower($senderAddress) !== strtolower($recipient['to'])) {
                $amount->plus((string) $recipient->amount);
            }
        }

        return $amount->toFloat();
    }

    private static function calculateAmountReceived(Model $transaction, bool $isMultiPayment, ?string $walletAddress, float $amount): float
    {
        if (! $isMultiPayment || $walletAddress === null) {
            return $amount;
        }

        $results = $transaction->multiPaymentRecipients
            ->filter(fn (MultiPayment $recipient) => strtolower($recipient->to) === strtolower($walletAddress));

        $amountValue = BigNumber::zero();
        foreach ($results as $recipient) {
            $amountValue->plus((string) $recipient->amount);
        }

        return $amountValue->toFloat();
    }

    private static function calculateFee(Model $transaction): float
    {
        return UnitConverter::formatUnits((string) $transaction->fee(), 'ark');
    }

    private static function calculateAmountWithFee(Model $transaction, float $fee): float
    {
        return $transaction->value->toFloat() + $fee;
    }

    private static function calculateAmountFiat(float $amount, int $timestamp): string
    {
        return ExchangeRate::convert($amount, $timestamp, true);
    }

    private static function calculateAmountReceivedFiat(float $amount, int $timestamp): string
    {
        return ExchangeRate::convert($amount, $timestamp);
    }

    private static function calculateFeeFiat(float $fee, int $timestamp): string
    {
        return ExchangeRate::convert($fee, $timestamp, true);
    }

    private static function resolveSenderWallet(Model $transaction): ?WalletDTO
    {
        $senderAddress = $transaction->sender_public_key !== ''
            ? Identity::address($transaction->sender_public_key)
            : $transaction->from;

        if ($senderAddress === '') {
            return null;
        }

        return WalletDTO::fromModel(Wallets::findByAddress($senderAddress));
    }

    private static function resolveRecipientWallet(Model $transaction, bool $isTransfer, bool $isTokenTransfer): ?WalletDTO
    {
        if (! $isTransfer && ! $isTokenTransfer) {
            return null;
        }

        $recipientAddress = self::resolveRecipientAddress($transaction);
        if ($recipientAddress === '') {
            return null;
        }

        return WalletDTO::fromModel(Wallets::findByAddress($recipientAddress));
    }

    private static function resolveRecipientAddress(Model $transaction): string
    {
        if ($transaction->to === null) {
            if ($transaction->deployed_contract_address !== null) {
                return $transaction->deployed_contract_address;
            }

            if ($transaction->sender_public_key !== '') {
                return Identity::address($transaction->sender_public_key);
            }

            return $transaction->from;
        }

        return $transaction->to;
    }

    private static function resolveValidatorRegistration(Model $transaction, bool $isValidatorResignation): ?self
    {
        if (! $isValidatorResignation) {
            return null;
        }

        /** @var ?Model $registration */
        $registration = Model::where('sender_public_key', $transaction->sender_public_key)
            ->withScope(ValidatorRegistrationScope::class)
            ->first();

        if ($registration === null) {
            return null;
        }

        return self::fromModel($registration);
    }

    private static function calculateIsSentToSelf(Model $transaction, TransactionMethod $method, string $address): bool
    {
        if (! $method->isTransfer() && ! $method->isTokenTransfer()) {
            return false;
        }

        $senderAddress = Identity::address($transaction->sender_public_key);
        if ($address !== $senderAddress) {
            return false;
        }

        if (! $method->isMultiPayment()) {
            $recipientAddress = self::resolveRecipientAddress($transaction);
            if ($recipientAddress !== null && $address !== $recipientAddress) {
                return false;
            }
        }

        return true;
    }

    private static function calculateIsSent(Model $transaction, string $address): bool
    {
        return Identity::address($transaction->sender_public_key) === $address;
    }

    private static function calculateIsReceived(Model $transaction, string $address): bool
    {
        return $transaction->to === $address;
    }
}
