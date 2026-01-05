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

it('should go to page 2', function ($resolution) {
    for ($i = 84831; $i < 84831 + 50; $i++) {
        Block::factory()->create([
            'number'    => $i,
            'timestamp' => Carbon::now()->subSeconds(800 - ($i * 8))->getTimestampMs(),
        ]);
    }

    $blocks = Block::orderBy('number', 'desc')->get();

    $this->browse(function (Browser $browser) use ($blocks, $resolution) {
        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('blocks')
            ->waitForText('50 results', ignoreCase: true)
            ->assertSee('Page 1 of 2');

        foreach ($blocks->take(25) as $block) {
            $browser->assertSee(number_format($block->number->toNumber()));
        }

        $browser->click('[data-testid="pagination:next-page"] button')
            ->waitForText('Page 2 of 2')
            ->assertQueryStringHas('page', '2');

        foreach ($blocks->skip(25)->take(25) as $block) {
            $browser->assertSee(number_format($block->number->toNumber()));
        }
    });
})->with('resolutions');

it('should reset to page 1 on per-page change', function ($resolution) {
    for ($i = 84831; $i < 84831 + 50; $i++) {
        Block::factory()->create([
            'number'    => $i,
            'timestamp' => Carbon::now()->subSeconds(800 - ($i * 8))->getTimestampMs(),
        ]);
    }

    $blocks = Block::orderBy('number', 'desc')->get();

    $this->browse(function (Browser $browser) use ($blocks, $resolution) {
        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('blocks', ['page' => 2])
            ->waitForText('50 results', ignoreCase: true)
            ->assertSee('Page 2 of 2');

        foreach ($blocks->skip(25)->take(25) as $block) {
            $browser->assertSee(number_format($block->number->toNumber()));
        }

        $browser->click('[data-testid="pagination:per-page-dropdown:button"]')
            ->waitForTextIn('[data-testid="pagination:per-page-dropdown:dropdown"]', '10')
            ->clickAtXPath('//div[@data-testid="pagination:per-page-dropdown:dropdown"]//span[.//text()="10"]')
            ->waitForText('Page 1 of 5');

        foreach ($blocks->take(10) as $block) {
            $browser->assertSee(number_format($block->number->toNumber()));
        }
    });
})->with('resolutions');

it('should show the correct decimal places for the stats', function ($decimalPlaces, $totalRewards, $resolution) {
    $transactionsCount = 23;

    Block::factory()->create([
        'timestamp'          => Carbon::now()->subHours(3)->getTimestampMs(),
        'reward'             => $totalRewards * 1e18,
        'transactions_count' => $transactionsCount,
    ]);

    Cache::flush();

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
