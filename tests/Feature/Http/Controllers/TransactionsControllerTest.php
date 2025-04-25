<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\BigNumber;
use App\Services\NumberFormatter;
use Carbon\Carbon;

it('should render the page without any errors', function () {
    $this
        ->get(route('transactions'))
        ->assertOk();
});

it('should get the transaction stats for the last 24 hours', function () {
    $this->travelTo('2021-04-14 16:02:04');

    Transaction::factory(148)->withReceipt()->create([
        'timestamp' => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'value'    => 123 * 1e18,
        'gas_price' => 5000000000,
    ]);

    Transaction::factory(12)->withReceipt()->create([
        'timestamp'  => Carbon::parse('2021-04-13 13:02:04')->getTimestampMs(),
        'value'     => 123 * 1e18,
        'gas_price'  => 5000000000,
    ]);

    $this
        ->get(route('transactions'))
        ->assertOk()
        ->assertViewHas([
            'transactionCount' => 148,
            'volume'           => 18204,
            'totalFees'        => 0.01554,
            'averageFee'       => 0.000105,
        ])
        ->assertSeeInOrder([
            'Transactions (24h)',
            '148',
            'Volume (24h)',
        ])
        ->assertSeeInOrder([
            'Volume (24h)',
            '18,204 DARK',
            'Total Fees (24h)',
        ])
        ->assertSeeInOrder([
            'Total Fees (24h)',
            '0.01554 DARK',
            'Average Fee (24h)',
        ])
        ->assertSeeInOrder([
            'Average Fee (24h)',
            '0.000105 DARK',
            'Showing 0 results', // alpine isn't triggered so nothing is shown in the table
        ]);

    $this->travelTo('2021-04-15 16:02:04');

    $this
        ->get(route('transactions'))
        ->assertOk()
        ->assertViewHas([
            'transactionCount' => 0,
            'volume'           => 0,
            'totalFees'        => 0,
            'averageFee'       => 0,
        ]);
});

it('should show the correct decimal places for the stats', function ($decimalPlaces, $amount, $fee, $expectedFormattedFee) {
    $this->travelTo('2021-04-14 16:02:04');

    $gasUsed = 21000;

    Transaction::factory()
        ->withReceipt(gasUsed: $gasUsed)
        ->create([
            'timestamp' => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
            'value'     => BigNumber::new($amount * 1e18),
            'gas_price' => $fee,
        ]);

    $formattedFee = $fee * $gasUsed;

    expect((string) $formattedFee)->toEqual($expectedFormattedFee);

    $fee = BigNumber::new($formattedFee)->toFloat();

    $this
        ->get(route('transactions'))
        ->assertOk()
        ->assertViewHas([
            'transactionCount' => 1,
            'volume'           => $amount,
            'totalFees'        => $fee,
            'averageFee'       => $fee,
        ])
        ->assertSeeInOrder([
            'Transactions (24h)',
            '1',
            'Volume (24h)',
        ])
        ->assertSeeInOrder([
            'Volume (24h)',
            number_format($amount, $decimalPlaces).' DARK',
            'Total Fees (24h)',
        ])
        ->assertSeeInOrder([
            'Total Fees (24h)',
            NumberFormatter::networkCurrency($fee, 8, withSuffix: true),
            'Average Fee (24h)',
        ])
        ->assertSeeInOrder([
            'Average Fee (24h)',
            NumberFormatter::networkCurrency($fee, 8, withSuffix: true),
            'Showing 0 results', // alpine isn't triggered so nothing is shown in the table
        ]);
})->with([
    8 => [8, 919123.48392049, 99184739, '2082879519000'],
    7 => [7, 919123.4839204, 99184730, '2082879330000'],
    6 => [6, 919123.483929, 99183900, '2082861900000'],
    5 => [5, 919123.48392, 99739000, '2094519000000'],
    4 => [4, 919123.4839, 99180000, '2082780000000'],
    3 => [3, 919123.489, 47900000, '1005900000000'],
    2 => [2, 919123.48, 99000000, '2079000000000'],
]);

it('should cache the transaction stats for 5 minutes', function () {
    $this->travelTo('2021-04-14 16:02:04');

    Transaction::factory(146)->withReceipt()->create([
        'timestamp'       => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'value'          => 123 * 1e18,
        'gas_price'       => 5000000000,
    ]);

    $volume = (123 * 146);

    $this
        ->get(route('transactions'))
        ->assertOk()
        ->assertViewHas([
            'transactionCount' => 146,
            'volume'           => $volume,
            'totalFees'        => 0.01533,
            'averageFee'       => 0.000105,
        ]);

    Transaction::factory(12)->withReceipt()->create([
        'timestamp'       => Carbon::parse('2021-04-14 13:03:04')->getTimestampMs(),
        'value'          => 123 * 1e18,
        'gas_price'       => 5000000000,
    ]);

    $this
        ->get(route('transactions'))
        ->assertOk()
        ->assertViewHas([
            'transactionCount' => 146,
            'volume'           => $volume,
            'totalFees'        => 0.01533,
            'averageFee'       => 0.000105,
        ]);

    $this->travelTo('2021-04-14 16:09:04');

    $volume += 123 * 12;

    $this
        ->get(route('transactions'))
        ->assertOk()
        ->assertViewHas([
            'transactionCount' => 158,
            'volume'           => $volume,
            'totalFees'        => 0.01659,
            'averageFee'       => 0.000105,
        ]);
});
