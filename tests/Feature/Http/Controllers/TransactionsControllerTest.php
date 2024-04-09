<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\BigNumber;
use App\Services\Timestamp;
use Carbon\Carbon;

it('should render the page without any errors', function () {
    $this
        ->get(route('transactions'))
        ->assertOk();
});

it('should get the transaction stats for the last 24 hours', function () {
    $this->travelTo('2021-04-14 16:02:04');

    Transaction::factory(148)->create([
        'timestamp' => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'amount'    => 123 * 1e8,
        'fee'       => 0.99 * 1e8,
    ]);

    Transaction::factory(12)->create([
        'timestamp' => Carbon::parse('2021-04-13 13:02:04')->getTimestampMs(),
        'amount'    => 123 * 1e8,
        'fee'       => 0.99 * 1e8,
    ]);

    $this
        ->get(route('transactions'))
        ->assertOk()
        ->assertViewHas([
            'transactionCount' => 148,
            'volume'           => 18204,
            'totalFees'        => 146.52,
            'averageFee'       => 0.99,
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
            '146.52 DARK',
            'Average Fee (24h)',
        ])
        ->assertSeeInOrder([
            'Average Fee (24h)',
            '0.99 DARK',
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

it('should show the correct decimal places for the stats', function ($decimalPlaces, $amount, $fee) {
    $this->travelTo('2021-04-14 16:02:04');

    Transaction::factory()->create([
        'timestamp' => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'amount'    => BigNumber::new($amount * 1e8),
        'fee'       => $fee * 1e8,
    ]);

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
            $fee.' DARK',
            'Average Fee (24h)',
        ])
        ->assertSeeInOrder([
            'Average Fee (24h)',
            $fee.' DARK',
            'Showing 0 results', // alpine isn't triggered so nothing is shown in the table
        ]);
})->with([
    8 => [8, 919123.48392049, 0.99184739],
    7 => [7, 919123.4839204, 0.9918473],
    6 => [6, 919123.483929, 0.991839],
    5 => [5, 919123.48392, 0.99739],
    4 => [4, 919123.4839, 0.9918],
    3 => [3, 919123.489, 0.479],
    2 => [2, 919123.48, 0.99],
]);

it('should cache the transaction stats for 5 minutes', function () {
    $this->travelTo('2021-04-14 16:02:04');

    Transaction::factory(146)->create([
        'timestamp' => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'amount'    => 123 * 1e8,
        'fee'       => 0.99 * 1e8,
    ]);

    Transaction::factory(2)->multiPayment()->create([
        'timestamp' => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'amount'    => (432 + 42) * 1e8,
        'fee'       => 0.99 * 1e8,
        'asset'     => [
            'payments' => [
                [
                    'amount' => 432 * 1e8,
                ],
                [
                    'amount' => 42 * 1e8,
                ],
            ],
        ],
    ]);

    $volume = (123 * 146) + ((432 + 42) * 2);

    $this
        ->get(route('transactions'))
        ->assertOk()
        ->assertViewHas([
            'transactionCount' => 148,
            'volume'           => $volume,
            'totalFees'        => 146.52,
            'averageFee'       => 0.99,
        ]);

    Transaction::factory(12)->create([
        'timestamp' => Carbon::parse('2021-04-14 13:03:04')->getTimestampMs(),
        'amount'    => 123 * 1e8,
        'fee'       => 0.99 * 1e8,
    ]);

    $this
        ->get(route('transactions'))
        ->assertOk()
        ->assertViewHas([
            'transactionCount' => 148,
            'volume'           => $volume,
            'totalFees'        => 146.52,
            'averageFee'       => 0.99,
        ]);

    $this->travelTo('2021-04-14 16:09:04');

    $volume += 123 * 12;

    $this
        ->get(route('transactions'))
        ->assertOk()
        ->assertViewHas([
            'transactionCount' => 160,
            'volume'           => $volume,
            'totalFees'        => 158.4,
            'averageFee'       => 0.99,
        ]);
});
