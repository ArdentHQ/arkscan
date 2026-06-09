<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\DTO\Inertia\Concerns\WithTokenApproval;
use App\Enums\TokenTransferArgument;
use App\Models\TokenAction;
use App\Models\Transaction as Model;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
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
        public int $confirmations,
        public ?string $transactionError,
        public bool $recipientIsContract,
        public ?string $validatorPublicKey,
        public ?string $username,
        #[LiteralTypeScriptType('{recipient: IWalletReference; amount: string | null} | null')]
        public ?array $tokenTransfer,
        #[LiteralTypeScriptType('{spender: IWalletReference; amount: string | null; isUnlimited: boolean; isRevoke: boolean} | null')]
        public ?array $tokenApproval,
        public ?Token $token,
        #[LiteralTypeScriptType('{recipient: IWalletReference; amount: string}[]')]
        public array $batchTokenTransfers,
    ) {
    }

    public static function fromModel(Model $transaction): self
    {
        $viewModel = new TransactionViewModel($transaction);
        $username  = $viewModel->isUsernameRegistration() ? $viewModel->username() : null;
        $recipient = $viewModel->recipient();
        $token     = (new WalletCache())->getToken($recipient->address());

        $tokenActionRecord  = null;
        $tokenActionRecords = null;
        if ($viewModel->isTokenTransfer() || $viewModel->isApprove() || $viewModel->isContractDeployment()) {
            $tokenActionRecord = TokenAction::with('token')
                ->where('transaction_hash', $transaction->hash)
                ->first();
        }

        if ($token !== null) {
            $token = Token::fromModel($token);
        } elseif ($viewModel->isBatchTransfer()) {
            $tokenActionRecords = TokenAction::with('token')
                ->where('transaction_hash', $transaction->hash)
                ->get();

            $firstRecord = $tokenActionRecords->first();
            if ($firstRecord?->token !== null) {
                $token = Token::fromModel($firstRecord->token);
            }
        } elseif ($viewModel->isTokenTransfer() || $viewModel->isApprove() || $viewModel->isContractDeployment()) {
            if ($tokenActionRecord?->token !== null) {
                $token = Token::fromModel($tokenActionRecord->token);
            }
        }

        $batchTokenTransfers = [];
        if ($viewModel->isBatchTransfer() && $tokenActionRecords !== null) {
            $addresses = $tokenActionRecords->pluck('to')->unique()->values()->all();

            $wallets = Wallet::whereIn('address', $addresses)
                ->get()
                ->keyBy('address');

            foreach ($tokenActionRecords as $tf) {
                $walletModel = $wallets->get($tf->to);
                $wallet      = $walletModel !== null
                    ? WalletReference::fromModel($walletModel)
                    : WalletReference::stub($tf->to);

                $batchTokenTransfers[] = [
                    'recipient' => $wallet,
                    'amount'    => (string) $tf->value,
                ];
            }
        }

        return new self(
            confirmations: $viewModel->confirmations(),
            transactionError: $viewModel->transactionError(),
            recipientIsContract: $recipient->isContract(),
            validatorPublicKey: $viewModel->validatorPublicKey(),
            username: $username,
            tokenTransfer: self::tokenTransferDetails($viewModel, $tokenActionRecord),
            tokenApproval: self::tokenApprovalDetails($viewModel),
            token: $token,
            batchTokenTransfers: $batchTokenTransfers,
        );
    }

    /**
     * @return array{recipient: WalletReference, amount: string|null}|null
     */
    private static function tokenTransferDetails(TransactionViewModel $transaction, ?TokenAction $tokenActionRecord): ?array
    {
        if (! $transaction->isTokenTransfer() && ! $transaction->isContractDeployment()) {
            return null;
        }

        if ($transaction->isTokenTransfer()) {
            $arguments = $transaction->methodArguments();
            if (count($arguments) === 0 || ! array_key_exists(TokenTransferArgument::RECIPIENT, $arguments)) {
                return null;
            }

            $amount = null;
            if (array_key_exists(TokenTransferArgument::AMOUNT, $arguments)) {
                $amount = (new ArgumentDecoder($arguments[TokenTransferArgument::AMOUNT]))->decodeUnsignedInt();
            }

            $recipientAddress = (new ArgumentDecoder($arguments[TokenTransferArgument::RECIPIENT]))->decodeAddress();
        } else {
            if ($tokenActionRecord === null) {
                return null;
            }

            $amount           = (string) $tokenActionRecord->value;
            $recipientAddress = $tokenActionRecord->to;
        }

        $recipientWallet     = Wallet::where('address', $recipientAddress)->first();
        $recipientWalletData = $recipientWallet !== null
            ? WalletReference::fromModel($recipientWallet)
            : WalletReference::stub($recipientAddress);

        return [
            'recipient' => $recipientWalletData,
            'amount'    => $amount,
        ];
    }
}
