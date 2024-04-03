<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\ForgingStats;
use Carbon\Carbon;

beforeEach(fn () => ForgingStats::truncate());

it('should render the page without any errors', function () {
    Block::factory()->create([
        'timestamp'    => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'reward'       => 2 * 1e8,
        'total_amount' => 904 * 1e8,
    ]);

    $this
        ->get(route('blocks'))
        ->assertOk();
});

it('should get the block stats for the last 24 hours', function () {
    $this->travelTo('2021-04-14 16:02:04');

    foreach (range(1, 19) as $seconds) {
        ForgingStats::factory()->create([
            'timestamp'     => Carbon::parse('2021-04-14 13:02:04')->subSecond($seconds)->getTimestampMs(),
            'missed_height' => 1,
        ]);
    }

    Block::factory(147)->create([
        'timestamp'    => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'reward'       => 2 * 1e8,
        'total_amount' => 13 * 1e8,
    ]);

    Block::factory()->create([
        'timestamp'    => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'reward'       => 2 * 1e8,
        'total_amount' => 904 * 1e8,
    ]);

    Block::factory(12)->create([
        'timestamp'    => Carbon::parse('2021-04-13 13:02:04')->getTimestampMs(),
        'reward'       => 2 * 1e8,
        'total_amount' => 123 * 1e8,
    ]);

    $this
        ->get(route('blocks'))
        ->assertOk()
        ->assertViewHas([
            'forgedCount'   => 148,
            'missedCount'   => 19,
            'totalRewards'  => 2 * 148,
            'largestAmount' => 904,
        ])
        ->assertSeeInOrder([
            'Blocks Produced (24h)',
            '148',
            'Missed Blocks (24h)',
        ])
        ->assertSeeInOrder([
            'Missed Blocks (24h)',
            '19',
            'Block Rewards (24h)',
        ])
        ->assertSeeInOrder([
            'Block Rewards (24h)',
            '296 DARK',
            'Largest Block (24h)',
        ])
        ->assertSeeInOrder([
            'Largest Block (24h)',
            '904 DARK',
            // TODO: uncomment when table is ready
            // 'Showing 0 results', // alpine isn't triggered so nothing is shown in the table
        ]);

    $this->travelTo('2021-04-15 16:02:04');

    $this
        ->get(route('blocks'))
        ->assertOk()
        ->assertViewHas([
            'transactionCount' => 0,
            'volume'           => 0,
            'totalFees'        => 0,
            'averageFee'       => 0,
        ]);
});

it('should show the correct decimal places for the stats', function ($decimalPlaces, $totalRewards, $largestAmount) {
    $this->travelTo('2021-04-14 16:02:04');

    Block::factory()->create([
        'timestamp'    => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'reward'       => $totalRewards * 1e8,
        'total_amount' => $largestAmount * 1e8,
    ]);

    $this
        ->get(route('blocks'))
        ->assertOk()
        ->assertViewHas([
            'forgedCount'   => 1,
            'missedCount'   => 0,
            'totalRewards'  => $totalRewards,
            'largestAmount' => $largestAmount,
        ])
        ->assertSeeInOrder([
            'Blocks Produced (24h)',
            '1',
            'Missed Blocks (24h)',
        ])
        ->assertSeeInOrder([
            'Missed Blocks (24h)',
            '0',
            'Block Rewards (24h)',
        ])
        ->assertSeeInOrder([
            'Block Rewards (24h)',
            number_format($totalRewards, $decimalPlaces).' DARK',
            'Largest Block (24h)',
        ])
        ->assertSeeInOrder([
            'Largest Block (24h)',
            number_format($largestAmount, $decimalPlaces).' DARK',
            // TODO: uncomment when table is ready
            // 'Showing 0 results', // alpine isn't triggered so nothing is shown in the table
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

    Block::factory(148)->create([
        'timestamp'    => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'reward'       => 2 * 1e8,
        'total_amount' => 13 * 1e8,
    ]);

    foreach (range(1, 19) as $seconds) {
        ForgingStats::factory()->create([
            'timestamp'     => Carbon::parse('2021-04-14 13:02:04')->subSecond($seconds)->getTimestampMs(),
            'missed_height' => 1,
        ]);
    }

    $this
        ->get(route('blocks'))
        ->assertOk()
        ->assertViewHas([
            'forgedCount'   => 148,
            'missedCount'   => 19,
            'totalRewards'  => 2 * 148,
            'largestAmount' => 13,
        ]);

    Block::factory(12)->create([
        'timestamp'    => Carbon::parse('2021-04-14 13:03:04')->getTimestampMs(),
        'reward'       => 2 * 1e8,
        'total_amount' => 14 * 1e8,
    ]);

    foreach (range(1, 2) as $seconds) {
        ForgingStats::factory()->create([
            'timestamp'     => Carbon::parse('2021-04-14 13:03:04')->subSecond($seconds)->getTimestampMs(),
            'missed_height' => 1,
        ]);
    }

    $this
        ->get(route('blocks'))
        ->assertOk()
        ->assertViewHas([
            'forgedCount'   => 148,
            'missedCount'   => 19,
            'totalRewards'  => 2 * 148,
            'largestAmount' => 13,
        ]);

    $this->travelTo('2021-04-14 16:09:04');

    $this
        ->get(route('blocks'))
        ->assertOk()
        ->assertViewHas([
            'forgedCount'   => 160,
            'missedCount'   => 21,
            'totalRewards'  => 2 * 160,
            'largestAmount' => 14,
        ]);
});
