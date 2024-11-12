<?php

declare(strict_types=1);

use App\Models\Receipt;
use App\Models\Transaction;
use App\Services\BigNumber;
use App\Services\Cache\StatisticsCache;
use ArkEcosystem\Crypto\Utils\UnitConverter;
use Carbon\Carbon;

it('should get fees', function () {
    $volume    = BigNumber::new(0);
    $totalFees = BigNumber::new(0);

    $transactionCount = 7;

    Transaction::factory($transactionCount)
        ->create([
            'timestamp' => Carbon::now()->subHours(1)->getTimestampMs(),
        ])
        ->each(function ($transaction, $index) use (&$volume, &$totalFees) {
            $transaction->gas_price = $index + 1;
            $transaction->save();

            $volume->plus($transaction->amount->valueOf());
            $totalFees->plus(BigNumber::new($transaction->gas_price->valueOf())->multipliedBy(21000)->valueOf());

            Receipt::factory()->create([
                'id'       => $transaction->id,
                'gas_used' => 21000,
            ]);
        });

    expect((new StatisticsCache())->getTransactionData())->toEqual([
        'transaction_count' => $transactionCount,
        'volume'            => UnitConverter::parseUnits((string) $volume, 'wei'),
        'total_fees'        => UnitConverter::parseUnits((string) $totalFees, 'gwei'),
        'average_fee'       => BigNumber::new(UnitConverter::parseUnits((string) $totalFees, 'gwei'))->toFloat($transactionCount),
    ]);
});
