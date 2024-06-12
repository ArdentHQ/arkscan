<?php

declare(strict_types=1);

use App\Enums\SortDirection;
use App\Http\Livewire\Delegates\RecentVotes;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Features\SupportLifecycleHooks\SupportLifecycleHooks;
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

function generateTransactions(): array
{
    $delegate1 = Wallet::factory()->activeDelegate()->create([
        'address'    => 'delegate-address-c',
        'attributes' => [
            'delegate' => [
                'username' => 'delegate-1',
            ],
        ],
    ]);

    $delegate2 = Wallet::factory()->activeDelegate()->create([
        'address'    => 'delegate-address-b',
        'attributes' => [
            'delegate' => [
                'username' => 'delegate-2',
            ],
        ],
    ]);

    $delegate3 = Wallet::factory()->activeDelegate()->create([
        'address'    => 'delegate-address-a',
        'attributes' => [
            'delegate' => [
                'username' => 'delegate-3',
            ],
        ],
    ]);

    $voteTransaction = Transaction::factory()->vote()->create([
        'timestamp' => Timestamp::fromUnix(Carbon::parse('2023-09-18 03:41:04')->unix())->unix(),
        'asset'     => [
            'vote' => ['+'.$delegate1->public_key],
        ],
    ]);

    $unvoteTransaction = Transaction::factory()->unvote()->create([
        'timestamp' => Timestamp::fromUnix(Carbon::parse('2023-09-18 04:41:04')->unix())->unix(),
        'asset'     => [
            'vote' => ['-'.$delegate2->public_key],
        ],
    ]);

    $voteSwapTransaction = Transaction::factory()->voteCombination()->create([
        'timestamp' => Timestamp::fromUnix(Carbon::parse('2023-09-18 05:41:04')->unix())->unix(),
        'asset'     => [
            'vote' => ['-'.$delegate1->public_key, '+'.$delegate3->public_key],
        ],
    ]);

    return [
        'delegate1'           => $delegate1,
        'delegate2'           => $delegate2,
        'delegate3'           => $delegate3,
        'voteTransaction'     => $voteTransaction,
        'unvoteTransaction'   => $unvoteTransaction,
        'voteSwapTransaction' => $voteSwapTransaction,
    ];
};

it('should sort by age descending by default', function () {
    $data = generateTransactions();

    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->assertSet('sortKey', 'age')
        ->assertSet('sortDirection', SortDirection::DESC)
        ->assertSeeInOrder([
            $data['voteSwapTransaction']->address,
            $data['unvoteTransaction']->address,
            $data['voteTransaction']->address,
            $data['voteSwapTransaction']->address,
            $data['unvoteTransaction']->address,
            $data['voteTransaction']->address,
        ]);
});

it('should sort age in ascending order', function () {
    $data = generateTransactions();

    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->call('sortBy', 'age')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            $data['voteTransaction']->address,
            $data['unvoteTransaction']->address,
            $data['voteSwapTransaction']->address,
            $data['voteTransaction']->address,
            $data['unvoteTransaction']->address,
            $data['voteSwapTransaction']->address,
        ]);
});

it('should sort name in ascending order', function () {
    $data = generateTransactions();

    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->call('sortBy', 'name')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            $data['voteTransaction']->address,
            $data['unvoteTransaction']->address,
            $data['voteSwapTransaction']->address,
            $data['voteTransaction']->address,
            $data['unvoteTransaction']->address,
            $data['voteSwapTransaction']->address,
        ]);
});

it('should sort name in descending order', function () {
    $data = generateTransactions();

    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->call('sortBy', 'name')
        ->call('sortBy', 'name')
        ->assertSet('sortDirection', SortDirection::DESC)
        ->assertSeeInOrder([
            $data['voteSwapTransaction']->address,
            $data['unvoteTransaction']->address,
            $data['voteTransaction']->address,
            $data['voteSwapTransaction']->address,
            $data['unvoteTransaction']->address,
            $data['voteTransaction']->address,
        ]);
});

it('should sort address in ascending order', function () {
    $data = generateTransactions();

    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->call('sortBy', 'address')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            $data['voteSwapTransaction']->address,
            $data['unvoteTransaction']->address,
            $data['voteTransaction']->address,
            $data['voteSwapTransaction']->address,
            $data['unvoteTransaction']->address,
            $data['voteTransaction']->address,
        ]);
});

it('should sort address in descending order', function () {
    $data = generateTransactions();

    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->call('sortBy', 'address')
        ->call('sortBy', 'address')
        ->assertSet('sortDirection', SortDirection::DESC)
        ->assertSeeInOrder([
            $data['voteTransaction']->address,
            $data['unvoteTransaction']->address,
            $data['voteSwapTransaction']->address,
            $data['voteTransaction']->address,
            $data['unvoteTransaction']->address,
            $data['voteSwapTransaction']->address,
        ]);
});

it('should sort type in ascending order', function () {
    $data = generateTransactions();

    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->call('sortBy', 'type')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            $data['unvoteTransaction']->address,
            $data['voteTransaction']->address,
            $data['voteSwapTransaction']->address,
            $data['unvoteTransaction']->address,
            $data['voteTransaction']->address,
            $data['voteSwapTransaction']->address,
        ]);
});

it('should sort type in descending order', function () {
    $data = generateTransactions();

    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->call('sortBy', 'type')
        ->call('sortBy', 'type')
        ->assertSet('sortDirection', SortDirection::DESC)
        ->assertSeeInOrder([
            $data['voteSwapTransaction']->address,
            $data['voteTransaction']->address,
            $data['unvoteTransaction']->address,
            $data['voteSwapTransaction']->address,
            $data['voteTransaction']->address,
            $data['unvoteTransaction']->address,
        ]);
});

it('should alternate sorting direction', function () {
    $component = Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->assertSet('sortKey', 'age')
        ->assertSet('sortDirection', SortDirection::DESC)
        ->call('sortBy', 'age')
        ->assertSet('sortKey', 'age')
        ->assertSet('sortDirection', SortDirection::ASC);

    foreach (['name', 'address', 'type'] as $column) {
        $component->call('sortBy', $column)
            ->assertSet('sortKey', $column)
            ->assertSet('sortDirection', SortDirection::ASC)
            ->call('sortBy', $column)
            ->assertSet('sortKey', $column)
            ->assertSet('sortDirection', SortDirection::DESC);
    }
});

it('should reset page on sorting change', function () {
    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->assertSet('paginators.page', 1)
        ->assertSet('sortKey', 'age')
        ->assertSet('sortDirection', SortDirection::DESC)
        ->call('gotoPage', 12)
        ->call('sortBy', 'age')
        ->assertSet('paginators.page', 1)
        ->assertSet('sortKey', 'age')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->call('gotoPage', 12)
        ->call('sortBy', 'age')
        ->assertSet('paginators.page', 1)
        ->assertSet('sortKey', 'age')
        ->assertSet('sortDirection', SortDirection::DESC);
});

it('should parse sorting direction from query string', function () {
    Route::get('/test-delegates', function () {
        return BladeCompiler::render('<livewire:delegates.recent-votes :defer-loading="false" />');
    });

    $data = generateTransactions();

    $this->get('/test-delegates?sort=type&sort-direction=asc')
        ->assertSeeInOrder([
            $data['unvoteTransaction']->address,
            $data['voteTransaction']->address,
            $data['voteSwapTransaction']->address,
            $data['unvoteTransaction']->address,
            $data['voteTransaction']->address,
            $data['voteSwapTransaction']->address,
        ]);

    $this->get('/test-delegates?sort=type&sort-direction=desc')
        ->assertSeeInOrder([
            $data['voteSwapTransaction']->address,
            $data['voteTransaction']->address,
            $data['unvoteTransaction']->address,
            $data['voteSwapTransaction']->address,
            $data['voteTransaction']->address,
            $data['unvoteTransaction']->address,
        ]);
});

it('should force default sort direction if invalid query string value', function () {
    Route::get('/test-delegates', function () {
        return BladeCompiler::render('<livewire:delegates.recent-votes :defer-loading="false" />');
    });

    $data = generateTransactions();

    $this->get('/test-delegates?sort=type&sort-direction=desc')
        ->assertSeeInOrder([
            $data['voteSwapTransaction']->address,
            $data['voteTransaction']->address,
            $data['unvoteTransaction']->address,
            $data['voteSwapTransaction']->address,
            $data['voteTransaction']->address,
            $data['unvoteTransaction']->address,
        ]);

    $this->get('/test-delegates?sort=type&sort-direction=testing')
        ->assertSeeInOrder([
            $data['unvoteTransaction']->address,
            $data['voteTransaction']->address,
            $data['voteSwapTransaction']->address,
            $data['unvoteTransaction']->address,
            $data['voteTransaction']->address,
            $data['voteSwapTransaction']->address,
        ]);
});
