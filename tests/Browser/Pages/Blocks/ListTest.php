<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->withoutExceptionHandling();

    $this->wallet = Wallet::factory()
        ->activeValidator()
        ->create();

    $attributes             = $this->wallet->getAttribute('attributes') ?? [];
    $attributes['username'] = 'dusk-validator';
    $this->wallet->setAttribute('attributes', $attributes);
    $this->wallet->save();
});

afterEach(function () {
    Cache::flush();
});

it('should show the correct decimal places for the stats', function ($decimalPlaces, $totalRewards, $resolution) {
    // $this->travelTo('2021-04-14 16:02:04');

    $transactionsCount = 23;

    Block::factory()->create([
        'timestamp'          => Carbon::now()->subHours(3)->getTimestampMs(),
        // 'timestamp'          => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'reward'             => $totalRewards * 1e18,
        'transactions_count' => $transactionsCount,
    ]);

    Cache::flush();

    // $this
    //     ->get(route('blocks-old'))
    //     ->assertOk()
    //     ->assertViewHas([
    //         'forgedCount'     => 1,
    //         'missedCount'     => 0,
    //         'totalRewards'    => $totalRewards,
    //         'maxTransactions' => $transactionsCount,
    //     ])
    //     ->assertSeeInOrder([
    //         'Blocks Produced (24h)',
    //         '1',
    //         'Missed Blocks (24h)',
    //     ])
    //     ->assertSeeInOrder([
    //         'Missed Blocks (24h)',
    //         '0',
    //         'Block Rewards (24h)',
    //     ])
    //     ->assertSeeInOrder([
    //         'Block Rewards (24h)',
    //         number_format($totalRewards, $decimalPlaces).' DARK',
    //         'Max Transactions (24h)',
    //     ])
    //     ->assertSeeInOrder([
    //         'Max Transactions (24h)',
    //         $transactionsCount,
    //         'Showing 0 results',
    //     ]);

    $this->browse(function (Browser $browser) use ($decimalPlaces, $totalRewards, $transactionsCount, $resolution) {
        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('blocks')
            ->waitForText('1 result', ignoreCase: true)
            ->waitForSeeInOrder([
                'Blocks Produced (24h)',
                '1',
                'Missed Blocks (24h)',
            ])
            ->waitForSeeInOrder([
                'Missed Blocks (24h)',
                '0',
                'Block Rewards (24h)',
            ])
            ->waitForSeeInOrder([
                'Block Rewards (24h)',
                number_format($totalRewards, $decimalPlaces).' DARK',
                'Max Transactions (24h)',
            ])
            ->waitForSeeInOrder([
                'Max Transactions (24h)',
                $transactionsCount,
                '1 results',
            ], ignoreCase: true);
            // ->waitForSeeInOrder([
            //     'Blocks Produced (24h)',
            //     '1',
            //     'Missed Blocks (24h)',
            //     '0',
            //     'Block Rewards (24h)',
            //     number_format($totalRewards, $decimalPlaces).' DARK',
            //     'Max Transactions (24h)',
            //     $transactionsCount,
            //     'Showing 0 results',
            // ], ignoreCase: true);
    });
})->with([
    8 => [8, 919123.48392049],
    7 => [7, 919123.4839204],
    6 => [6, 919123.483929],
    5 => [5, 919123.48392],
    4 => [4, 919123.4839],
    3 => [3, 919123.489],
    2 => [2, 919123.48],
])->with('resolutions');

dataset('resolutions', [
    'desktop' => [['width' => 1280, 'height' => 1024]],
    'lg'      => [['width' => 1024, 'height' => 768]],
    'md-lg'   => [['width' => 960, 'height' => 667]],
    'md'      => [['width' => 768, 'height' => 1024]],
    'sm'      => [['width' => 640, 'height' => 960]],
    'xs'      => [['width' => 370, 'height' => 844]],
]);
