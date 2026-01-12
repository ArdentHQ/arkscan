<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\Enums\TokenTransferArgument;
use App\Models\MultiPayment;
use App\Models\Transaction as Model;
use App\Services\ExchangeRate;
use App\ViewModels\TransactionViewModel;
use ArkEcosystem\Crypto\Utils\Abi\ArgumentDecoder;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('ITransactionDetails')]
class TransactionDetails extends Data
{
    public function __construct(
        public int $confirmations,
        public ?string $transactionError,
        public bool $recipientIsContract,
        public ?string $validatorPublicKey,
        public ?string $username,
        #[LiteralTypeScriptType('{recipient: string; amount: string | null} | null')]
        public ?array $tokenTransfer,
        #[LiteralTypeScriptType('{formatted: string | null; utf8: string | null; raw: string | null} | null')]
        public ?array $payload,
        #[LiteralTypeScriptType('{address: string; amount: string}[]')]
        public array $multiPaymentRecipients,
        public string $totalFiat,
        public float $totalFiatValue,
    ) {
    }

    public static function fromModel(Model $transaction): self
    {
        $viewModel = new TransactionViewModel($transaction);

        return new self(
            confirmations: $viewModel->confirmations(),
            transactionError: $viewModel->transactionError(),
            recipientIsContract: $viewModel->recipient()?->isContract() ?? false,
            validatorPublicKey: $viewModel->validatorPublicKey(),
            username: $viewModel->username(),
            tokenTransfer: self::tokenTransferDetails($viewModel),
            payload: self::payloadDetails($viewModel),
            multiPaymentRecipients: self::multiPaymentRecipients($viewModel),
            totalFiat: $viewModel->totalFiat(true),
            totalFiatValue: ExchangeRate::convertNumerical($viewModel->amountWithFee(), $transaction->timestamp),
        );
    }

    /**
     * @return array{recipient: string, amount: string|null}|null
     */
    private static function tokenTransferDetails(TransactionViewModel $transaction): ?array
    {
        if (! $transaction->isTokenTransfer()) {
            return null;
        }

        $arguments = $transaction->methodArguments();
        if (count($arguments) === 0 || ! array_key_exists(TokenTransferArgument::RECIPIENT, $arguments)) {
            return null;
        }

        $recipient = (new ArgumentDecoder($arguments[TokenTransferArgument::RECIPIENT]))->decodeAddress();

        $amount = null;
        if (array_key_exists(TokenTransferArgument::AMOUNT, $arguments)) {
            $amount = (new ArgumentDecoder($arguments[TokenTransferArgument::AMOUNT]))->decodeUnsignedInt();
        }

        return [
            'recipient' => $recipient,
            'amount'    => $amount,
        ];
    }

    /**
     * @return array{formatted: ?string, utf8: ?string, raw: ?string}|null
     */
    private static function payloadDetails(TransactionViewModel $transaction): ?array
    {
        if (! $transaction->hasPayload()) {
            return null;
        }

        return [
            'formatted' => $transaction->formattedPayload(),
            'utf8'      => $transaction->utf8Payload(),
            'raw'       => $transaction->rawPayload(),
        ];
    }

    /**
     * @return array<int, array{address: string, amount: string}>
     */
    private static function multiPaymentRecipients(TransactionViewModel $transaction): array
    {
        return $transaction->multiPaymentRecipients()
            ->map(fn (MultiPayment $recipient) => [
                'address' => $recipient->to,
                'amount'  => (string) $recipient->amount,
            ])
            ->values()
            ->toArray();
    }
}
