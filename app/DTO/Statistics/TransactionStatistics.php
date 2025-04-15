<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

use App\Models\Block;
use App\Models\Transaction;
use Livewire\Wireable;

final class TransactionStatistics implements Wireable
{
    public function __construct(
        public array $details,
        public TransactionAveragesStatistics $averages,
        public TransactionRecordsStatistics $records,
    ) {
        //
    }

    public static function make(
        array $details,
        TransactionAveragesStatistics $averages,
        TransactionRecordsStatistics $records,
    ): self {
        return new self(
            $details,
            $averages,
            $records,
        );
    }

    public function toLivewire(): array
    {
        $records = $this->records;

        return [
            'details'  => $this->details,
            'averages' => $this->averages->toArray(),

            'records'  => [
                'largest_transaction'        => $this->records->largestTransaction?->hash(),
                'largest_block'              => $this->records->largestBlock?->hash(),
                'highest_fee'                => $this->records->blockWithHighestFees?->hash(),
                'most_transactions_in_block' => $this->records->blockWithMostTransactions?->hash(),
            ],
        ];
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    public static function fromLivewire($value)
    {
        return new self(
            $value['details'],
            TransactionAveragesStatistics::make([
                'count'  => $value['averages']['transactions'],
                'amount' => $value['averages']['transaction_volume'],
                'fee'    => $value['averages']['transaction_fees'],
            ]),
            TransactionRecordsStatistics::make(
                $value['records']['largest_transaction'] !== null ? Transaction::firstWhere('id', $value['records']['largest_transaction']) : null,
                $value['records']['largest_block'] !== null ? Block::firstWhere('id', $value['records']['largest_block']) : null,
                $value['records']['highest_fee'] !== null ? Block::firstWhere('id', $value['records']['highest_fee']) : null,
                $value['records']['most_transactions_in_block'] !== null ? Block::firstWhere('id', $value['records']['most_transactions_in_block']) : null,
            ),
        );
    }
}
