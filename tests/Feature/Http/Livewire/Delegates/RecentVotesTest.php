<?php

declare(strict_types=1);

use App\Http\Livewire\Delegates\RecentVotes;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Timestamp;
use Carbon\Carbon;
use Livewire\Livewire;

it('should render', function () {
    Livewire::test(RecentVotes::class)
        ->assertSet('isReady', false)
        ->assertSee('Showing 0 results');
});

it('should render with votes', function () {
    $this->travelTo(Carbon::parse('2020-03-21 18:42:00'));

    $wallet   = Wallet::factory()->create();
    $delegate = Wallet::factory()->activeDelegate()->create();

    Transaction::factory(27)->vote()->create([
        'timestamp'         => Timestamp::fromUnix(Carbon::parse('2020-03-21 14:12:00')->unix())->unix(),
        'sender_public_key' => $wallet->public_key,
        'asset'             => [
            'votes' => ['+'.$delegate->public_key],
        ],
    ]);

    Livewire::test(RecentVotes::class)
        ->assertSee('Showing 0 results')
        ->call('setIsReady')
        ->assertSee('Showing 27 results');
});

it('should not render votes older than 30 days', function () {
    $this->travelTo(Carbon::parse('2020-04-21 18:42:00'));

    $wallet   = Wallet::factory()->create();
    $delegate = Wallet::factory()->activeDelegate()->create();

    Transaction::factory(27)->vote()->create([
        'timestamp'         => Timestamp::fromUnix(Carbon::parse('2020-03-21 14:12:00')->unix())->unix(),
        'sender_public_key' => $wallet->public_key,
        'asset'             => [
            'votes' => ['+'.$delegate->public_key],
        ],
    ]);

    Transaction::factory(4)->vote()->create([
        'timestamp'         => Timestamp::fromUnix(Carbon::parse('2020-04-20 14:12:00')->unix())->unix(),
        'sender_public_key' => $wallet->public_key,
        'asset'             => [
            'votes' => ['+'.$delegate->public_key],
        ],
    ]);

    Livewire::test(RecentVotes::class)
        ->assertSee('Showing 0 results')
        ->call('setIsReady')
        ->assertSee('Showing 4 results');
});

it('should not defer loading if disabled', function () {
    Livewire::test(RecentVotes::class, ['deferLoading' => false])
        ->assertSet('isReady', true)
        ->assertSee('Showing 0 results');
});

it('should show no results message if no votes', function () {
    Livewire::test(RecentVotes::class, ['deferLoading' => false])
        ->assertSee(trans('tables.recent-votes.no_results.no_results'));
});
