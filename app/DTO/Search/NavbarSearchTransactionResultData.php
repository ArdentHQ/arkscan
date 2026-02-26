<?php

declare(strict_types=1);

namespace App\DTO\Search;

use App\DTO\MemoryWallet;
use App\Models\Transaction;
use App\Services\Transactions\TransactionMethod;
use App\ViewModels\TransactionViewModel;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('INavbarSearchTransactionResultData')]
final class NavbarSearchTransactionResultData extends Data
{
    public function __construct(
        public string $hash,
        public float $amountWithFee,
        public bool $isVote,
        public bool $isUnvote,
        public bool $isTransfer,
        public bool $isTokenTransfer,
        public NavbarSearchMemoryWalletData $sender,
        public NavbarSearchMemoryWalletData $recipient,
        public string $typeName,
        public ?string $votedValidatorLabel,
    ) {
    }

    public static function fromModel(Transaction $transaction): self
    {
        $transactionMethod = new TransactionMethod($transaction);

        $viewModel = new TransactionViewModel($transaction);

        $votedValidatorLabel = null;
        if ($transactionMethod->isVote()) {
            $votedValidator = $viewModel->voted();
            if ($votedValidator !== null) {
                $votedValidatorLabel = $votedValidator->username() ?? $votedValidator->address();
            }
        }

        /** @var NavbarSearchMemoryWalletData $sender */
        $sender = NavbarSearchMemoryWalletData::fromMemoryWallet(MemoryWallet::fromPublicKey($transaction->sender_public_key));

        /** @var NavbarSearchMemoryWalletData $recipient */
        $recipient = NavbarSearchMemoryWalletData::fromMemoryWallet(MemoryWallet::fromAddress($transaction->recipientAddress()));

        return new self(
            hash: $transaction->hash,
            amountWithFee: $viewModel->amountWithFee(),
            isVote: $transactionMethod->isVote(),
            isUnvote: $transactionMethod->isUnvote(),
            isTransfer: $transactionMethod->isTransfer(),
            isTokenTransfer: $transactionMethod->isTokenTransfer(),
            sender: $sender,
            recipient: $recipient,
            typeName: $transactionMethod->name(),
            votedValidatorLabel: $votedValidatorLabel,
        );
    }
}
