<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\Actions\CacheNetworkHeight;
use App\DTO\MemoryWallet;
use App\Enums\TokenTransferArgument;
use App\Models\MultiPayment;
use App\Models\Transaction as Model;
use App\Services\ExchangeRate;
use App\Services\Timestamp;
use App\Services\Transactions\TransactionMethod;
use ArkEcosystem\Crypto\Utils\Abi\ArgumentDecoder;
use ArkEcosystem\Crypto\Utils\UnitConverter;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('ITransactionDetails')]
class TransactionDetails extends Data
{
    public function __construct(
        public string $timestampFormatted,
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
        $method          = new TransactionMethod($transaction);
        $methodArguments = $method->arguments();

        return new self(
            timestampFormatted: Timestamp::fromUnixHuman($transaction->timestamp),
            confirmations: self::confirmations($transaction),
            transactionError: self::transactionError($transaction),
            recipientIsContract: self::recipientIsContract($transaction),
            validatorPublicKey: self::validatorPublicKey($method, $methodArguments),
            username: self::username($method, $methodArguments),
            tokenTransfer: self::tokenTransferDetails($method, $methodArguments),
            payload: self::payloadDetails($transaction),
            multiPaymentRecipients: self::multiPaymentRecipients($transaction),
            totalFiat: self::totalFiat($transaction),
            totalFiatValue: self::totalFiatValue($transaction),
        );
    }

    /**
     * @return array{recipient: string, amount: string|null}|null
     */
    private static function tokenTransferDetails(TransactionMethod $method, array $arguments): ?array
    {
        if (! $method->isTokenTransfer()) {
            return null;
        }

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
    private static function payloadDetails(Model $transaction): ?array
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

    private static function amountWithFee(Model $transaction): float
    {
        $fee = UnitConverter::formatUnits((string) $transaction->fee(), 'ark');

        return $transaction->value->toFloat() + $fee;
    }

    private static function confirmations(Model $transaction): int
    {
        return abs(CacheNetworkHeight::execute() - $transaction->block_number);
    }

    private static function transactionError(Model $transaction): ?string
    {
        return $transaction->transactionError();
    }

    private static function username(TransactionMethod $method, array $arguments): ?string
    {
        return $method->isUsernameRegistration()
            ? self::decodeUsername($arguments)
            : null;
    }

    private static function totalFiat(Model $transaction): string
    {
        return ExchangeRate::convert(self::amountWithFee($transaction), $transaction->timestamp, true);
    }

    private static function totalFiatValue(Model $transaction): float
    {
        return ExchangeRate::convertNumerical(self::amountWithFee($transaction), $transaction->timestamp);
    }

    private static function recipientIsContract(Model $transaction): bool
    {
        $recipient = self::resolveRecipient($transaction);

        return $recipient?->isContract() ?? false;
    }

    private static function resolveRecipient(Model $transaction): ?MemoryWallet
    {
        if (is_null($transaction->to)) {
            if ($transaction->deployed_contract_address !== null) {
                return MemoryWallet::fromAddress($transaction->deployed_contract_address);
            }

            return MemoryWallet::fromPublicKey($transaction->sender_public_key);
        }

        return MemoryWallet::fromAddress($transaction->to);
    }

    private static function validatorPublicKey(TransactionMethod $method, array $arguments): ?string
    {
        if (! $method->isValidatorRegistration() && ! $method->isValidatorUpdate()) {
            return null;
        }

        return $arguments[0] ?? null;
    }

    private static function decodeUsername(array $arguments): ?string
    {
        if (count($arguments) === 0) {
            return null;
        }

        return (new ArgumentDecoder(implode($arguments)))->decodeString();
    }
}
