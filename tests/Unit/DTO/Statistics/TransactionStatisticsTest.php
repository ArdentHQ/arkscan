<?php

use App\DTO\Statistics\TransactionAveragesStatistics;
use App\DTO\Statistics\TransactionRecordsStatistics;
use App\DTO\Statistics\TransactionStatistics;
use App\Enums\StatsTransactionType;
use App\Models\Block;
use App\Models\Transaction;
use App\Services\Cache\TransactionCache;

it('should convert to and from wireable array', function () {
    $cache = new TransactionCache();
    $details = StatsTransactionType::all()
        ->mapWithKeys(fn ($type) => [$type => $cache->getHistoricalByType($type)])
        ->toArray();

    $transaction  = Transaction::factory()->create();
    $largestBlock = Block::factory()->create();
    $highestFeeBlock = Block::factory()->create();
    $mostTransactionsBlock = Block::factory()->create();

    $averages = TransactionAveragesStatistics::make([
        'count'  => 1,
        'amount' => 100.42134589,
        'fee'    => 1.42134589,
    ]);

    $records = TransactionRecordsStatistics::make(
        $transaction,
        $largestBlock,
        $highestFeeBlock,
        $mostTransactionsBlock,
    );

    $subject = TransactionStatistics::make(
        $details,
        $averages,
        $records,
    );

    expect($subject->toLivewire())->toBe([
        'details' => $details,

        'averages' => [
            'transactions'       => 1,
            'transaction_volume' => '100.42134589 DARK',
            'transaction_fees'   => '1.42134589 DARK',
        ],

        'records' => [
            'largest_transaction'        => $transaction->id,
            'largest_block'              => $largestBlock->id,
            'highest_fee'                => $highestFeeBlock->id,
            'most_transactions_in_block' => $mostTransactionsBlock->id,
        ],
    ]);

    $subject = TransactionStatistics::fromLivewire($subject->toLivewire());

    expect($subject->details)->toEqual($details);
    expect($subject->averages->count)->toEqual(1);
    expect($subject->averages->volume)->toEqual('100.42134589 DARK');
    expect($subject->averages->fees)->toEqual('1.42134589 DARK');
    expect($subject->records->largestTransaction->id())->toEqual($transaction->id);
    expect($subject->records->largestBlock->id())->toEqual($largestBlock->id);
    expect($subject->records->blockWithHighestFees->id())->toEqual($highestFeeBlock->id);
    expect($subject->records->blockWithMostTransactions->id())->toEqual($mostTransactionsBlock->id);
});
