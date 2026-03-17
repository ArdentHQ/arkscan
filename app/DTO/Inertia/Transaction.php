<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\DTO\Inertia\Concerns\WithTokenApproval;
use App\Models\MultiPayment;
use App\Models\Transaction as Model;
use App\Services\ExchangeRate;
use App\ViewModels\TransactionViewModel;
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
        #[LiteralTypeScriptType('{address: string; amount: string}[]')]
        public array $multiPaymentRecipients,
        #[LiteralTypeScriptType('Record<string, number>')]
        public array $exchangeRates,
        public string $url,
        #[LiteralTypeScriptType('{ functionName: string | null, methodId: string | null, arguments: string[] | null }')]
        public array $methodData,
        #[LiteralTypeScriptType('{
            spender: IWalletReference;
            amount: string | null;
            isUnlimited: boolean;
            isRevoke: boolean;
        } | null')]
        public ?array $tokenApprovalDetails,
        public ?self $validatorRegistration,
        public ?string $votedFor,
        public ?string $votedForUsername,
        public ?WalletReference $sender,
        public ?WalletReference $recipient,
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
        $senderWallet = null;
        if ($transaction->relationLoaded('senderWallet') || $transaction->relationLoaded('sender')) {
            $senderWallet = $transaction->relationLoaded('senderWallet') ? $transaction->senderWallet : null;
            $senderWallet ??= $transaction->relationLoaded('sender') ? $transaction->sender : null;
        } else {
            $senderWallet = $transaction->senderWallet;
        }

        $sender = $senderWallet !== null
            ? WalletReference::fromModel($senderWallet)
            : WalletReference::stub($transaction->from);

        $recipientWallet = $transaction->recipientWallet;

        $recipient = $recipientWallet !== null
            ? WalletReference::fromModel($recipientWallet)
            : WalletReference::stub($transaction->recipientAddress());

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
            timestamp: $transaction->timestamp->unix(),
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
            exchangeRates: ExchangeRate::allCurrencyRates($transaction->timestamp),
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
