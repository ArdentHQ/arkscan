<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\BigNumber;
use App\Services\Cache\StatisticsCache;
use ArkEcosystem\Crypto\Utils\UnitConverter;
use Carbon\Carbon;

it('should get fees', function () {
    $volume    = BigNumber::zero();
    $totalFees = BigNumber::zero();

    $transactionCount = 7;

    Transaction::factory($transactionCount)
        ->create([
            'timestamp' => Carbon::now()->subHours(1)->getTimestampMs(),
            'value'     => '100000000000000000000',
            'gas_used'  => 21000,
        ])
        ->each(function ($transaction, $index) use (&$volume, &$totalFees) {
            $transaction->gas_price = BigNumber::new($index + 1);
            $transaction->save();

            $volume->plus($transaction->value->valueOf());
            $totalFees->plus(BigNumber::new($transaction->gas_price->valueOf())->multipliedBy(21000)->valueOf());
        });

    $formattedVolume = UnitConverter::parseUnits((string) $volume, 'wei');

    expect((new StatisticsCache())->getTransactionData())->toEqual([
        'transaction_count' => $transactionCount,
        'volume'            => (string) $formattedVolume,
        'total_fees'        => '588000',
        'average_fee'       => 588000 / $transactionCount,
    ]);
});
