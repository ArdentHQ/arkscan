<?php

declare(strict_types=1);

namespace App\DTO\Search;

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
        public ?NavbarSearchMemoryWalletData $sender,
        public ?NavbarSearchMemoryWalletData $recipient,
        public string $typeName,
        public ?string $votedValidatorLabel,
    ) {
    }

    public static function fromViewModel(TransactionViewModel $transaction): self
    {
        $votedValidatorLabel = null;

        if ($transaction->isVote()) {
            $votedValidator = $transaction->voted();
            if ($votedValidator !== null) {
                $votedValidatorLabel = $votedValidator->username() ?? $votedValidator->address();
            }
        }

        return new self(
            hash: $transaction->hash(),
            amountWithFee: $transaction->amountWithFee(),
            isVote: $transaction->isVote(),
            isUnvote: $transaction->isUnvote(),
            isTransfer: $transaction->isTransfer(),
            isTokenTransfer: $transaction->isTokenTransfer(),
            sender: NavbarSearchMemoryWalletData::fromMemoryWallet($transaction->sender()),
            recipient: NavbarSearchMemoryWalletData::fromMemoryWallet($transaction->recipient()),
            typeName: $transaction->typeName(),
            votedValidatorLabel: $votedValidatorLabel,
        );
    }
}
