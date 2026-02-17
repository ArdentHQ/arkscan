<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\DTO\Inertia\Concerns\WithTokenApproval;
use App\DTO\Inertia\Wallet as WalletDTO;
use App\Facades\Wallets;
use App\Models\Transaction as Model;
use App\ViewModels\TransactionViewModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('ITransaction')]
class Transaction extends Data
{
    use WithTokenApproval;

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
        public string $url,
        #[LiteralTypeScriptType('{ functionName: string | null, methodId: string | null, arguments: Record<string, string> }')]
        public array $methodData,
        #[LiteralTypeScriptType('{
            spender: string;
            amount: string | null;
            isUnlimited: boolean;
            isRevoke: boolean;
            spenderUsername: string | null;
            spenderHasUsername: boolean;
        } | null')]
        public ?array $tokenApprovalDetails,
        public bool $isSelfReceiving,
        public bool $isSent,
        public bool $isSentToSelf,
        public bool $isReceived,
        public ?self $validatorRegistration,
        public ?string $votedFor,
        public ?string $votedForUsername,
        public ?WalletDTO $sender,
        public ?WalletDTO $recipient,
    ) {
    }

    public static function fromModel(Model $transaction, ?string $address = null): self
    {
        $address = $address ?? $transaction->from;

        $viewModel = new TransactionViewModel($transaction);

        $votedFor         = null;
        $votedForUsername = null;
        if ($viewModel->isVote()) {
            $votedFor = $viewModel->voted();
            if ($votedFor !== null) {
                $votedForUsername = $votedFor->username();
                $votedFor         = $votedFor->address();
            }
        }

        $sender       = null;
        $senderWallet = $transaction->relationLoaded('senderWallet') ? $transaction->senderWallet : null;
        $senderWallet ??= $transaction->relationLoaded('sender') ? $transaction->sender : null;

        if ($senderWallet === null) {
            $senderAddress = $transaction->from;

            if ($senderAddress !== null) {
                try {
                    $senderWallet = Wallets::findByAddress($senderAddress);
                } catch (ModelNotFoundException) {
                    $sender = WalletDTO::stub($senderAddress);
                }
            }
        }

        if ($senderWallet !== null) {
            $sender = WalletDTO::fromModel($senderWallet);
        }

        $recipient = null;

        $recipientWallet = $transaction->relationLoaded('recipientWallet') ? $transaction->recipientWallet : null;
        if ($recipientWallet !== null) {
            $recipient = WalletDTO::fromModel($recipientWallet);
        } elseif ($transaction->relationLoaded('recipientWallet') && $transaction->to !== null) {
            $recipient = WalletDTO::stub($transaction->to);
        } else {
            $recipientAddress = $transaction->recipientAddress();

            if ($recipientAddress !== null) {
                try {
                    $recipientWallet = Wallets::findByAddress($recipientAddress);
                    $recipient       = WalletDTO::fromModel($recipientWallet);
                } catch (ModelNotFoundException) {
                    $recipient = WalletDTO::stub($recipientAddress);
                }
            }
        }

        $validatorRegistration            = null;
        $validatorRegistrationTransaction = $viewModel->validatorRegistration();
        if ($validatorRegistrationTransaction !== null) {
            $validatorRegistration = self::fromModel($validatorRegistrationTransaction->model());
        }

        $methodData = [
            'functionName' => null,
            'methodId' => null,
            'arguments' => null,
        ];
        $methodDataRaw = $transaction->getMethodData(true);
        if ($methodDataRaw !== null) {
            $methodData = [
                'functionName' => $methodDataRaw[0] ?? null,
                'methodId' => $methodDataRaw[1] ?? null,
                'arguments' => $methodDataRaw[2] ?? null,
            ];
        } else {
            $methodData['methodId'] = $transaction->methodHash();
        }

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
            amountReceived: $viewModel->amountReceived($address),
            amountFiat: $viewModel->amountFiat(true),
            amountReceivedFiat: $viewModel->amountReceivedFiat($address),
            fee: $viewModel->fee(),
            feeFiat: $viewModel->feeFiat(true),
            url: route('transaction', $transaction->hash),
            methodData: $methodData,
            tokenApprovalDetails: static::tokenApprovalDetails($viewModel),
            isSelfReceiving: $viewModel->isSelfReceiving(),
            isSent: $viewModel->isSent($address),
            isSentToSelf: $viewModel->isSentToSelf($address),
            isReceived: $viewModel->isReceived($address),
            validatorRegistration: $validatorRegistration,
            votedFor: $votedFor,
            votedForUsername: $votedForUsername,
            sender: $sender,
            recipient: $recipient,
        );
    }
}
