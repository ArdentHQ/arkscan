<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\DTO\Inertia\Concerns\WithTokenApproval;
use App\DTO\Inertia\Wallet as WalletDTO;
use App\Facades\Wallets;
use App\Models\MultiPayment;
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
        public int $timestamp,  // unix seconds for frontend
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
        #[LiteralTypeScriptType('{address: string; amount: string}[]')]
        public array $multiPaymentRecipients,
        public int | string $amountFiat,
        public int | string $amountReceivedFiat,
        public int | string $feeFiat,
        public string $url,
        #[LiteralTypeScriptType('{ functionName: string | null, methodId: string | null, arguments: string[] | null }')]
        public array $methodData,
        #[LiteralTypeScriptType('{
            spender: IWallet;
            amount: string | null;
            isUnlimited: boolean;
            isRevoke: boolean;
        } | null')]
        public ?array $tokenApprovalDetails,
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
            try {
                $senderWallet = Wallets::findByAddress($transaction->from);
            } catch (ModelNotFoundException) {
                $sender = WalletDTO::stub($transaction->from);
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

            try {
                $recipientWallet = Wallets::findByAddress($recipientAddress);
                $recipient       = WalletDTO::fromModel($recipientWallet);
            } catch (ModelNotFoundException) {
                $recipient = WalletDTO::stub($recipientAddress);
            }
        }

        $validatorRegistration            = null;
        $validatorRegistrationTransaction = $viewModel->validatorRegistration();
        if ($validatorRegistrationTransaction !== null) {
            $validatorRegistration = self::fromModel($validatorRegistrationTransaction->model());
        }

        $methodData = [
            'functionName' => null,
            'methodId'     => null,
            'arguments'    => null,
        ];
        $methodDataRaw = $transaction->getMethodData(true);
        if ($methodDataRaw !== null) {
            $methodData = [
                'functionName' => $methodDataRaw[0] ?? null,
                'methodId'     => $methodDataRaw[1] ?? null,
                'arguments'    => $methodDataRaw[2] ?? null,
            ];
        } else {
            $methodData['methodId'] = $transaction->methodHash();
        }

        return new self(
            hash: $transaction->hash,
            block_hash: $transaction->block_hash,
            block_number: $transaction->block_number,
            transaction_index: $transaction->transaction_index,
            timestamp: (int) $transaction->timestamp->timestamp,
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
            multiPaymentRecipients: self::multiPaymentRecipients($transaction),
            amountFiat: $viewModel->amountFiat(true),
            amountReceivedFiat: $viewModel->amountReceivedFiat($address),
            feeFiat: $viewModel->feeFiat(true),
            url: route('transaction', $transaction->hash),
            methodData: $methodData,
            tokenApprovalDetails: static::tokenApprovalDetails($viewModel),
            validatorRegistration: $validatorRegistration,
            votedFor: $votedFor,
            votedForUsername: $votedForUsername,
            sender: $sender,
            recipient: $recipient,
        );
    }

    /**
     * @return array<int, array{address: string, amount: string}>
     */
    private static function multiPaymentRecipients(Model $transaction): array
    {
        return $transaction->multiPaymentRecipients
            ->map(fn (MultiPayment $recipient) => [
                'address' => $recipient->to,
                'amount'  => (string) $recipient->amount,
            ])
            ->values()
            ->toArray();
    }
}
