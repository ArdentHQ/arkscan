<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\DTO\Inertia\Wallet as WalletDTO;
use App\Facades\Wallets;
use App\Models\Transaction as Model;
// use App\Models\Wallet;
use App\ViewModels\TransactionViewModel;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('ITransaction')]
class Transaction extends Data
{
    public function __construct(
        public string $hash,
        public string $block_hash,
        public int $block_number,
        public int $transaction_index,
        public int $timestamp,
        public int $nonce,
        public string $sender_public_key,
        public string $from,
        public string $to,
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
        public ?WalletDTO $sender,
        public ?WalletDTO $recipient,
    ) {}

    public static function fromModel(Model $transaction, ?string $address = null): self
    {
        $viewModel = new TransactionViewModel($transaction);

        $votedFor = null;
        if ($viewModel->isVote()) {
            $votedFor = $viewModel->voted();
            if ($votedFor !== null) {
                $votedFor = $votedFor->address();
            }
        }

        $sender        = null;
        $senderAddress = $viewModel->sender()?->address();
        if ($senderAddress !== null) {
            $senderWallet = Wallets::findByAddress($senderAddress);

            $sender = WalletDTO::fromModel($senderWallet);
        }

        $recipient        = null;
        $recipientAddress = $viewModel->recipient()?->address();
        if ($recipientAddress !== null) {
            $recipientWallet = Wallets::findByAddress($recipientAddress);

            $recipient = WalletDTO::fromModel($recipientWallet);
        }

        $validatorRegistration            = null;
        $validatorRegistrationTransaction = $viewModel->validatorRegistration();
        if ($validatorRegistrationTransaction !== null) {
            $validatorRegistration = self::fromModel($validatorRegistrationTransaction->model());
        }

        $address = $address ?? $transaction->from;

        return new self(
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
            amount: $viewModel->amount(),
            amountForItself: $viewModel->amountForItself(),
            amountExcludingItself: $viewModel->amountExcludingItself(),
            amountWithFee: $viewModel->amountWithFee(),
            amountReceived: $viewModel->amountReceived(),
            amountFiat: $viewModel->amountFiat(true),
            amountReceivedFiat: $viewModel->amountReceivedFiat($address),
            fee: $viewModel->fee(),
            feeFiat: $viewModel->feeFiat(true),
            type: $viewModel->typeName(),
            isTransfer: $viewModel->isTransfer(),
            isTokenTransfer: $viewModel->isTokenTransfer(),
            isVote: $viewModel->isVote(),
            isUnvote: $viewModel->isUnvote(),
            isValidatorRegistration: $viewModel->isValidatorRegistration(),
            isValidatorResignation: $viewModel->isValidatorResignation(),
            isValidatorUpdate: $viewModel->isValidatorUpdate(),
            isUsernameRegistration: $viewModel->isUsernameRegistration(),
            isUsernameResignation: $viewModel->isUsernameResignation(),
            isContractDeployment: $viewModel->isContractDeployment(),
            isMultiPayment: $viewModel->isMultiPayment(),
            isSelfReceiving: $viewModel->isSelfReceiving(),
            isSent: $viewModel->isSent($address),
            isSentToSelf: $viewModel->isSentToSelf($address),
            isReceived: $viewModel->isReceived($address),
            hasFailedStatus: $viewModel->hasFailedStatus(),
            validatorRegistration: $validatorRegistration,
            votedFor: $votedFor,
            sender: $sender,
            recipient: $recipient,
        );
    }
}
