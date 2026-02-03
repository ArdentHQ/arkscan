<?php

declare(strict_types=1);

use App\DTO\Statistics\TransactionAveragesStatistics;

it('should create statistics object correctly', function () {
    $averages = TransactionAveragesStatistics::make([
        'count'  => 1,
        'amount' => 100.42134589,
        'fee'    => 1.42134589,
    ]);

    expect($averages->count)->toEqual(1);
    expect($averages->volume)->toEqual('100.42134589 DARK');
    expect($averages->fees)->toEqual('1.42134589 DARK');

    expect($averages->toArray())->toBe([
        'transactions'       => 1,
        'transaction_volume' => '100.42134589 DARK',
        'transaction_fees'   => '1.42134589 DARK',
    ]);
});
