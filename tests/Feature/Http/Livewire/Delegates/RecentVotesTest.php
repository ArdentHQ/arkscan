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

it('should toggle all filters when "select all" is selected', function () {
    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->assertSet('filter', [
            'vote'      => true,
            'unvote'    => true,
            'vote-swap' => true,
        ])
        ->assertSet('selectAllFilters', true)
        ->set('filter.vote', true)
        ->assertSet('selectAllFilters', true)
        ->set('selectAllFilters', false)
        ->assertSet('filter', [
            'vote'      => false,
            'unvote'    => false,
            'vote-swap' => false,
        ])
        ->set('selectAllFilters', true)
        ->assertSet('filter', [
            'vote'      => true,
            'unvote'    => true,
            'vote-swap' => true,
        ]);
});

it('should toggle "select all" when all filters are selected', function () {
    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->assertSet('filter', [
            'vote'      => true,
            'unvote'    => true,
            'vote-swap' => true,
        ])
        ->assertSet('selectAllFilters', true)
        ->set('filter.vote', false)
        ->assertSet('selectAllFilters', false)
        ->set('filter.vote', true)
        ->assertSet('selectAllFilters', true);
});

it('should filter vote transactions', function () {
    $sender        = Wallet::factory()->create();
    $delegate      = Wallet::factory()->activeDelegate(false)->create();
    $otherDelegate = Wallet::factory()->activeDelegate(false)->create();

    $vote = Transaction::factory()->vote()->create([
        'sender_public_key' => $sender->public_key,
        'timestamp'         => Timestamp::now()->subMinute(1)->unix(),
        'asset'             => [
            'votes' => ['+'.$delegate->public_key],
        ],
    ]);

    $unvote = Transaction::factory()->unvote()->create([
        'sender_public_key' => $sender->public_key,
        'timestamp'         => Timestamp::now()->subMinute(1)->unix(),
        'asset'             => [
            'votes' => ['-'.$delegate->public_key],
        ],
    ]);

    $voteSwap = Transaction::factory()->voteCombination()->create([
        'sender_public_key' => $sender->public_key,
        'timestamp'         => Timestamp::now()->subMinute(1)->unix(),
        'asset'             => [
            'votes' => ['-'.$delegate->public_key, '+'.$otherDelegate->public_key],
        ],
    ]);

    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->set('filter', [
            'vote'      => true,
            'unvote'    => false,
            'vote-swap' => false,
        ])
        ->assertSee($vote->id)
        ->assertDontSee($unvote->id)
        ->assertDontSee($voteSwap->id);
});

it('should filter unvote transactions', function () {
    $sender        = Wallet::factory()->create();
    $delegate      = Wallet::factory()->activeDelegate(false)->create();
    $otherDelegate = Wallet::factory()->activeDelegate(false)->create();

    $vote = Transaction::factory()->vote()->create([
        'sender_public_key' => $sender->public_key,
        'timestamp'         => Timestamp::now()->subMinute(1)->unix(),
        'asset'             => [
            'votes' => ['+'.$delegate->public_key],
        ],
    ]);

    $unvote = Transaction::factory()->unvote()->create([
        'sender_public_key' => $sender->public_key,
        'timestamp'         => Timestamp::now()->subMinute(1)->unix(),
        'asset'             => [
            'votes' => ['-'.$delegate->public_key],
        ],
    ]);

    $voteSwap = Transaction::factory()->voteCombination()->create([
        'sender_public_key' => $sender->public_key,
        'timestamp'         => Timestamp::now()->subMinute(1)->unix(),
        'asset'             => [
            'votes' => ['-'.$delegate->public_key, '+'.$otherDelegate->public_key],
        ],
    ]);

    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->set('filter', [
            'vote'      => false,
            'unvote'    => true,
            'vote-swap' => false,
        ])
        ->assertSee($unvote->id)
        ->assertDontSee($vote->id)
        ->assertDontSee($voteSwap->id);
});

it('should filter vote swap transactions', function () {
    $sender        = Wallet::factory()->create();
    $delegate      = Wallet::factory()->activeDelegate(false)->create();
    $otherDelegate = Wallet::factory()->activeDelegate(false)->create();

    $vote = Transaction::factory()->vote()->create([
        'sender_public_key' => $sender->public_key,
        'timestamp'         => Timestamp::now()->subMinute(1)->unix(),
        'asset'             => [
            'votes' => ['+'.$delegate->public_key],
        ],
    ]);

    $unvote = Transaction::factory()->unvote()->create([
        'sender_public_key' => $sender->public_key,
        'timestamp'         => Timestamp::now()->subMinute(1)->unix(),
        'asset'             => [
            'votes' => ['-'.$delegate->public_key],
        ],
    ]);

    $voteSwap = Transaction::factory()->voteCombination()->create([
        'sender_public_key' => $sender->public_key,
        'timestamp'         => Timestamp::now()->subMinute(1)->unix(),
        'asset'             => [
            'votes' => ['-'.$delegate->public_key, '+'.$otherDelegate->public_key],
        ],
    ]);

    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->set('filter', [
            'vote'      => false,
            'unvote'    => false,
            'vote-swap' => true,
        ])
        ->assertSee($voteSwap->id)
        ->assertDontSee($vote->id)
        ->assertDontSee($unvote->id);
});

it('should show correct message when no filters are selected', function () {
    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->set('filter', [
            'vote'      => false,
            'unvote'    => false,
            'vote-swap' => false,
        ])
        ->assertSee(trans('tables.recent-votes.no_results.no_filters'));
});

it('should show correct message when there are no results', function () {
    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->assertSee(trans('tables.recent-votes.no_results.no_results'));
});
