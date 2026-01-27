<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

use App\DTO\Inertia\Transaction as TransactionDTO;
use App\Models\Block;
use App\Models\Transaction;
use App\ViewModels\BlockViewModel;

final class TransactionRecordsStatistics
{
    public ?TransactionDTO $largestTransaction = null;

    public ?BlockViewModel $blockWithHighestFees = null;

    public ?BlockViewModel $blockWithMostTransactions = null;

    public function __construct(
        ?Transaction $largestTransaction = null,
        ?Block $blockWithHighestFees = null,
        ?Block $blockWithMostTransactions = null,
    ) {
        if ($largestTransaction !== null) {
            $this->largestTransaction = TransactionDTO::fromModel($largestTransaction);
        }

        if ($blockWithHighestFees !== null) {
            $this->blockWithHighestFees = new BlockViewModel($blockWithHighestFees);
        }

        if ($blockWithMostTransactions !== null) {
            $this->blockWithMostTransactions = new BlockViewModel($blockWithMostTransactions);
        }
    }

    public static function make(
        ?Transaction $largestTransaction = null,
        ?Block $blockWithHighestFees = null,
        ?Block $blockWithMostTransactions = null,
    ): self {
        return new self(
            $largestTransaction,
            $blockWithHighestFees,
            $blockWithMostTransactions,
        );
    }

    public function toArray(): array
    {
        return [
            'largest_transaction'        => $this->largestTransaction,
            'highest_fee'                => $this->blockWithHighestFees,
            'most_transactions_in_block' => $this->blockWithMostTransactions,
        ];
    }
}
