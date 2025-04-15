<?php

declare(strict_types=1);

use App\Models\Receipt;
use App\Models\Transaction;
use App\Services\BigNumber;
use App\Services\Cache\StatisticsCache;
use ArkEcosystem\Crypto\Utils\UnitConverter;
use Brick\Math\BigDecimal;
use Carbon\Carbon;

it('should get fees', function () {
    $volume    = BigNumber::zero();
    $totalFees = BigNumber::zero();

    $transactionCount = 7;

    Transaction::factory($transactionCount)
        ->create([
            'timestamp' => Carbon::now()->subHours(1)->getTimestampMs(),
            'value'     => '100000000000000000000',
        ])
        ->each(function ($transaction, $index) use (&$volume, &$totalFees) {
            $transaction->gas_price = BigNumber::new($index + 1);
            $transaction->save();

            $volume->plus($transaction->value->valueOf());
            $totalFees->plus(BigNumber::new($transaction->gas_price->valueOf())->multipliedBy(21000)->valueOf());

            Receipt::factory()->create([
                'transaction_hash' => $transaction->hash,
                'gas_used'         => 21000,
            ]);
        });

    $formattedVolume    = UnitConverter::parseUnits((string) $volume, 'wei');
    $formattedTotalFees = UnitConverter::parseUnits((string) $totalFees, 'gwei');

    expect($formattedVolume)->toEqual(BigDecimal::of('700000000000000000000'));
    expect($formattedTotalFees)->toEqual(BigDecimal::of('588000000000000'));

    expect((new StatisticsCache())->getTransactionData())->toEqual([
        'transaction_count' => $transactionCount,
        'volume'            => (string) $formattedVolume,
        'total_fees'        => $formattedTotalFees,
        'average_fee'       => $formattedTotalFees->dividedBy($transactionCount),
    ]);
});
