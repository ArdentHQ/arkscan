<?php

declare(strict_types=1);

use App\Http\Livewire\Delegates\HeaderStats;
use App\Models\ForgingStats;
use App\Models\Round;
use App\Models\Wallet;
use App\Services\Cache\DelegateCache;
use App\Services\Cache\WalletCache;
use Livewire\Livewire;

beforeEach(function () {
    $this->wallets = Wallet::factory(51)
        ->activeDelegate()
        ->create()
        ->each(function ($wallet) {
            Round::factory()->create([
                'round'      => '112168',
                'public_key' => $wallet->public_key,
            ]);
        });
});

it('should render without errors', function () {
    $component = Livewire::test(HeaderStats::class);

    $component->assertHasNoErrors();
    $component->assertViewIs('livewire.delegates.header-stats');
});

it('should not error if no delegate data', function () {
    foreach ($this->wallets as $wallet) {
        expect((new WalletCache())->getDelegate($wallet->public_key))->toBeNull();
    }

    Livewire::test(HeaderStats::class)
        ->assertViewHasAll([
            'voterCount'      => 0,
            'totalVoted'      => 0,
            'votesPercentage' => 0,
            'missedBlocks'    => 0,
            'delegatesMissed' => 0,
        ]);
});

it('should show the correct number of votes', function () {
    (new DelegateCache())->setTotalWalletsVoted(25);
    (new DelegateCache())->setTotalBalanceVoted(200);

    Livewire::test(HeaderStats::class)
        ->assertViewHas('voterCount', 25)
        ->assertViewHas('totalVoted', 200);
});

it('should pluralize missed delegate count', function ($count, $text) {
    ForgingStats::truncate();

    ForgingStats::factory($count)->create([
        'forged' => false,
    ]);

    Livewire::test(HeaderStats::class)
        ->assertViewHas('delegatesMissed', $count)
        ->assertSeeInOrder([
            'Missed Blocks (30 Days)',
            $count,
            $count,
            $text,
            'View',
            'Voting (',
        ]);
})->with([
    0 => [0, 'Delegates'],
    1 => [1, 'Delegate'],
    2 => [2, 'Delegates'],
    3 => [3, 'Delegates'],
    4 => [4, 'Delegates'],
]);
