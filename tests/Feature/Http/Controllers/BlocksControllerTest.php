<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\ForgingStats;
use App\Models\Transaction;
use Carbon\Carbon;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    $block = Block::factory()->create([
        'timestamp' => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'reward'    => 2 * 1e18,
    ]);

    Transaction::factory()->create([
        'value'      => 904 * 1e18,
        'block_hash' => $block->hash,
    ]);

    $this
        ->get(route('blocks'))
        ->assertOk();
});

it('should get the block stats for the last 24 hours', function () {
    $this->travelTo('2021-04-14 16:02:04');

    Block::factory()->create([
        'timestamp'          => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'reward'             => 2 * 1e18,
        'transactions_count' => 904,
    ]);

    foreach (range(1, 19) as $seconds) {
        ForgingStats::factory()->create([
            'timestamp'     => Carbon::parse('2021-04-14 13:02:04')->subSecond($seconds)->getTimestampMs(),
            'missed_height' => 1,
        ]);
    }

    $blocks = Block::factory(147)->create([
        'timestamp' => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'reward'    => 2 * 1e18,
    ]);

    foreach ($blocks as $block) {
        Transaction::factory()->create([
            'block_hash' => $block->hash,
            'value'      => 13 * 1e18,
        ]);
    }

    $block = Block::factory()->create([
        'timestamp' => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'reward'    => 2 * 1e18,
    ]);

    Transaction::factory()->create([
        'block_hash' => $block->hash,
        'value'      => 904 * 1e18,
    ]);

    $blocks = Block::factory(12)->create([
        'timestamp' => Carbon::parse('2021-04-13 13:02:04')->getTimestampMs(),
        'reward'    => 2 * 1e18,
    ]);

    foreach ($blocks as $block) {
        Transaction::factory()->create([
            'block_hash' => $block->hash,
            'value'      => 123 * 1e18,
        ]);
    }

    $blockCount = Block::where('timestamp', '>', Carbon::parse('2021-04-13 16:02:04')->getTimestampMs())->count();

    $this
        ->get(route('blocks'))
        ->assertOk()
        ->assertViewHas([
            'forgedCount'     => $blockCount,
            'missedCount'     => 19,
            'totalRewards'    => 2 * $blockCount,
            'maxTransactions' => 904,
        ])
        ->assertSeeInOrder([
            'Blocks Produced (24h)',
            $blockCount,
            'Missed Blocks (24h)',
        ])
        ->assertSeeInOrder([
            'Missed Blocks (24h)',
            '19',
            'Block Rewards (24h)',
        ])
        ->assertSeeInOrder([
            'Block Rewards (24h)',
            (2 * $blockCount).' DARK',
            'Max Transactions (24h)',
        ])
        ->assertSeeInOrder([
            'Max Transactions (24h)',
            '904',
            'Showing 0 results',
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

it('should show the correct decimal places for the stats', function ($decimalPlaces, $totalRewards) {
    $this->travelTo('2021-04-14 16:02:04');

    $transactionsCount = 24;

    Block::factory()->create([
        'timestamp'          => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'reward'             => $totalRewards * 1e18,
        'transactions_count' => $transactionsCount,
    ]);

    $this
        ->get(route('blocks'))
        ->assertOk()
        ->assertViewHas([
            'forgedCount'     => 1,
            'missedCount'     => 0,
            'totalRewards'    => $totalRewards,
            'maxTransactions' => $transactionsCount,
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
            'Max Transactions (24h)',
        ])
        ->assertSeeInOrder([
            'Max Transactions (24h)',
            $transactionsCount,
            'Showing 0 results',
        ]);
})->with([
    8 => [8, 919123.48392049],
    7 => [7, 919123.4839204],
    6 => [6, 919123.483929],
    5 => [5, 919123.48392],
    4 => [4, 919123.4839],
    3 => [3, 919123.489],
    2 => [2, 919123.48],
]);

it('should cache the transaction stats for 5 minutes', function () {
    $this->travelTo('2021-04-14 16:02:04');

    $blocks = Block::factory(148)->create([
        'timestamp'          => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'reward'             => 2 * 1e18,
        'transactions_count' => 13,
    ]);

    foreach ($blocks as $block) {
        Transaction::factory()
            ->create([
                'block_hash'   => $block->hash,
                'block_number' => $block->number,
                'value'        => 13 * 1e18,
            ]);
    }

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
            'forgedCount'     => 148,
            'missedCount'     => 19,
            'totalRewards'    => 2 * 148,
            'maxTransactions' => 13,
        ]);

    $blocks = Block::factory(12)->create([
        'timestamp'          => Carbon::parse('2021-04-14 13:03:04')->getTimestampMs(),
        'reward'             => 2 * 1e18,
        'transactions_count' => 24,
    ]);

    foreach ($blocks as $block) {
        Transaction::factory()
            ->create([
                'block_hash'   => $block->hash,
                'block_number' => $block->number,
                'value'        => 14 * 1e18,
            ]);
    }

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
            'forgedCount'     => 148,
            'missedCount'     => 19,
            'totalRewards'    => 2 * 148,
            'maxTransactions' => 13,
        ]);

    $this->travelTo('2021-04-14 16:09:04');

    $this
        ->get(route('blocks'))
        ->assertOk()
        ->assertViewHas([
            'forgedCount'     => 160,
            'missedCount'     => 21,
            'totalRewards'    => 2 * 160,
            'maxTransactions' => 24,
        ]);
});
