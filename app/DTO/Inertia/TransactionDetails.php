<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\DTO\Inertia\Concerns\WithTokenApproval;
use App\DTO\Inertia\Wallet as WalletDTO;
use App\Enums\TokenTransferArgument;
use App\Facades\Wallets;
use App\Models\MultiPayment;
use App\Models\TokenTransfer as TokenTransferModel;
use App\Models\Transaction as Model;
use App\Services\Cache\WalletCache;
use App\Services\ExchangeRate;
use App\Services\Timestamp;
use App\ViewModels\TransactionViewModel;
use ArkEcosystem\Crypto\Utils\Abi\ArgumentDecoder;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('ITransactionDetails')]
class TransactionDetails extends Data
{
    use WithTokenApproval;

    public function __construct(
        public string $timestampFormatted,
        public int $confirmations,
        public ?string $transactionError,
        public bool $recipientIsContract,
        public ?string $validatorPublicKey,
        public ?string $username,
        #[LiteralTypeScriptType('{recipient: string; amount: string | null; recipientUsername: string | null; recipientHasUsername: boolean} | null')]
        public ?array $tokenTransfer,
        #[LiteralTypeScriptType('{spender: string; amount: string | null; isUnlimited: boolean; isRevoke: boolean; spenderUsername: string | null; spenderHasUsername: boolean} | null')]
        public ?array $tokenApproval,
        public ?Token $token,
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
        $username  = $viewModel->isUsernameRegistration() ? $viewModel->username() : null;

        $recipient = $viewModel->recipient();
        $token     = $recipient !== null ? (new WalletCache())->getToken($recipient->address()) : null;
        if ($token !== null) {
            $token = Token::fromModel($token);
        } elseif ($viewModel->isTokenTransfer() || $viewModel->isApprove()) {
            $tokenTransferRecord = TokenTransferModel::with('token')
                ->where('transaction_hash', $transaction->hash)
                ->first();

            if ($tokenTransferRecord?->token !== null) {
                $token = Token::fromModel($tokenTransferRecord->token);
            }
        }

        return new self(
            timestampFormatted: Timestamp::fromUnixHuman($transaction->timestamp),
            confirmations: $viewModel->confirmations(),
            transactionError: $viewModel->transactionError(),
            recipientIsContract: $recipient?->isContract() ?? false,
            validatorPublicKey: $viewModel->validatorPublicKey(),
            username: $username,
            tokenTransfer: self::tokenTransferDetails($viewModel),
            tokenApproval: self::tokenApprovalDetails($viewModel),
            token: $token,
            payload: self::payloadDetails($viewModel),
            multiPaymentRecipients: self::multiPaymentRecipients($viewModel),
            totalFiat: $viewModel->totalFiat(true),
            totalFiatValue: ExchangeRate::convertNumerical($viewModel->amountWithFee(), $transaction->timestamp),
        );
    }

    /**
     * @return array{recipient: string, amount: string|null, recipientUsername: string|null, recipientHasUsername: bool}|null
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

        $recipientWallet     = Wallets::findByAddress($recipient);
        $recipientWalletData = WalletDTO::fromModel($recipientWallet);

        return [
            'recipient'            => $recipient,
            'amount'               => $amount,
            'recipientUsername'    => $recipientWalletData->username,
            'recipientHasUsername' => $recipientWalletData->hasUsername,
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
            // Ensure invalid UTF-8 does not break JSON serialization.
            'formatted' => self::safeUtf8($transaction->formattedPayload() ?? ''),
            'utf8'      => self::safeUtf8($transaction->utf8Payload() ?? ''),
            'raw'       => self::safeUtf8($transaction->rawPayload() ?? ''),
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

    private static function safeUtf8(string $value): string
    {
        // Skip normalization when the payload is already valid UTF-8.
        if (preg_match('//u', $value) === 1) {
            return $value;
        }

        // Invalid UTF-8 in payloads breaks JSON encoding and causes Inertia JSON.parse errors;
        // normalize to the replacement character to keep the response valid.
        $previousSubstitute = mb_substitute_character();
        mb_substitute_character(0xFFFD);
        $converted = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        mb_substitute_character($previousSubstitute);

        return $converted;
    }
}
