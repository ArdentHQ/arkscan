<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

use App\Models\Block;
use App\Models\Transaction;
use App\ViewModels\BlockViewModel;
use App\ViewModels\TransactionViewModel;

final class TransactionRecordsStatistics
{
    public ?TransactionViewModel $largestTransaction = null;

    public ?BlockViewModel $largestBlock = null;

    public ?BlockViewModel $blockWithHighestFees = null;

    public ?BlockViewModel $blockWithMostTransactions = null;

    public function __construct(
        ?Transaction $largestTransaction = null,
        ?Block $largestBlock = null,
        ?Block $blockWithHighestFees = null,
        ?Block $blockWithMostTransactions = null,
    ) {
        if ($largestTransaction !== null) {
            $this->largestTransaction = new TransactionViewModel($largestTransaction);
        }

        if ($largestBlock !== null) {
            $this->largestBlock = new BlockViewModel($largestBlock);
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
        ?Block $largestBlock = null,
        ?Block $blockWithHighestFees = null,
        ?Block $blockWithMostTransactions = null,
    ): self {
        return new self(
            $largestTransaction,
            $largestBlock,
            $blockWithHighestFees,
            $blockWithMostTransactions,
        );
    }

    public function toArray(): array
    {
        return [
            'largest_transaction'        => $this->largestTransaction,
            'largest_block'              => $this->largestBlock,
            'highest_fee'                => $this->blockWithHighestFees,
            'most_transactions_in_block' => $this->blockWithMostTransactions,
        ];
    }
}
