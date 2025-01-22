<?php

declare(strict_types=1);

use App\Enums\SortDirection;
use App\Http\Livewire\Validators\RecentVotes;
use App\Models\Transaction;
use App\Models\Wallet;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;

function generateTransactions(): array
{
    $validator1 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username' => 'validator-1',
        ],
    ]);

    $validator2 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username' => 'validator-2',
        ],
    ]);

    $validator3 = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username' => 'validator-3',
        ],
    ]);

    $voteTransaction = Transaction::factory()->vote($validator1->address)->create([
        'timestamp' => Carbon::parse('2023-09-18 03:41:04')->getTimestampMs(),
        'sender_address' => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
    ]);

    $unvoteTransaction = Transaction::factory()->unvote()->create([
        'timestamp' => Carbon::parse('2023-09-18 04:41:04')->getTimestampMs(),
        'sender_address' => '0x38b4a84773bC55e88D07cBFC76444C2A37600084',
    ]);

    return [
        'validator1'           => $validator1,
        'validator2'           => $validator2,
        'validator3'           => $validator3,
        'voteTransaction'      => $voteTransaction,
        'unvoteTransaction'    => $unvoteTransaction,
    ];
};

beforeEach(fn () => $this->travelTo('2023-09-20 05:41:04'));

it('should render', function () {
    Livewire::test(RecentVotes::class)
        ->assertSet('isReady', false)
        ->assertSee('Showing 0 results');
});

it('should render with votes', function () {
    $this->travelTo(Carbon::parse('2020-03-21 18:42:00'));

    $wallet    = Wallet::factory()->create();
    $validator = Wallet::factory()->activeValidator()->create();

    Transaction::factory(27)->vote($validator->address)->create([
        'timestamp'         => Carbon::parse('2020-03-21 14:12:00')->getTimestampMs(),
        'sender_public_key' => $wallet->public_key,
    ]);

    Livewire::test(RecentVotes::class)
        ->assertSee('Showing 0 results')
        ->call('setIsReady')
        ->assertSee('Showing 27 results');
});

it('should not render votes older than 30 days', function () {
    $this->travelTo(Carbon::parse('2020-04-21 18:42:00'));

    $wallet    = Wallet::factory()->create();
    $validator = Wallet::factory()->activeValidator()->create();

    Transaction::factory(27)->vote($validator->address)->create([
        'timestamp'         => Carbon::parse('2020-03-21 14:12:00')->getTimestampMs(),
        'sender_public_key' => $wallet->public_key,
    ]);

    Transaction::factory(4)->vote($validator->address)->create([
        'timestamp'         => Carbon::parse('2020-04-20 14:12:00')->getTimestampMs(),
        'sender_public_key' => $wallet->public_key,
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
            'vote'   => true,
            'unvote' => true,
        ])
        ->assertSet('selectAllFilters', true)
        ->set('filter.vote', true)
        ->assertSet('selectAllFilters', true)
        ->set('selectAllFilters', false)
        ->assertSet('filter', [
            'vote'   => false,
            'unvote' => false,
        ])
        ->set('selectAllFilters', true)
        ->assertSet('filter', [
            'vote'   => true,
            'unvote' => true,
        ]);
});

it('should toggle "select all" when all filters are selected', function () {
    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->assertSet('filter', [
            'vote'   => true,
            'unvote' => true,
        ])
        ->assertSet('selectAllFilters', true)
        ->set('filter.vote', false)
        ->assertSet('selectAllFilters', false)
        ->set('filter.vote', true)
        ->assertSet('selectAllFilters', true);
});

it('should filter vote transactions', function () {
    $sender         = Wallet::factory()->create();
    $validator      = Wallet::factory()->activeValidator()->create();
    $otherValidator = Wallet::factory()->activeValidator()->create();

    $vote = Transaction::factory()->vote($validator->address)->create([
        'sender_public_key' => $sender->public_key,
        'timestamp'         => Carbon::now()->subMinute(1)->getTimestampMs(),
    ]);

    $unvote = Transaction::factory()->unvote()->create([
        'sender_public_key' => $sender->public_key,
        'timestamp'         => Carbon::now()->subMinute(1)->getTimestampMs(),
    ]);

    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->set('filter', [
            'vote'   => true,
            'unvote' => false,
        ])
        ->assertSee($vote->id)
        ->assertDontSee($unvote->id);
});

it('should filter unvote transactions', function () {
    $sender         = Wallet::factory()->create();
    $validator      = Wallet::factory()->activeValidator()->create();
    $otherValidator = Wallet::factory()->activeValidator()->create();

    $vote = Transaction::factory()->vote($validator->address)->create([
        'sender_public_key' => $sender->public_key,
        'timestamp'         => Carbon::now()->subMinute(1)->getTimestampMs(),
    ]);

    $unvote = Transaction::factory()->unvote()->create([
        'sender_public_key' => $sender->public_key,
        'timestamp'         => Carbon::now()->subMinute(1)->getTimestampMs(),
    ]);

    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->set('filter', [
            'vote'   => false,
            'unvote' => true,
        ])
        ->assertSee($unvote->id)
        ->assertDontSee($vote->id);
});

it('should show correct message when no filters are selected', function () {
    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->set('filter', [
            'vote'   => false,
            'unvote' => false,
        ])
        ->assertSee(trans('tables.recent-votes.no_results.no_filters'));
});

it('should show correct message when there are no results', function () {
    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->assertSee(trans('tables.recent-votes.no_results.no_results'));
});

it('should sort by age descending by default', function () {
    $data = generateTransactions();

    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->assertSet('sortKey', 'age')
        ->assertSet('sortDirection', SortDirection::DESC)
        ->assertSeeInOrder([
            'vote-item*'.$data['unvoteTransaction']->id,
            $data['unvoteTransaction']->id,
            'vote-item*'.$data['voteTransaction']->id,
            $data['voteTransaction']->id,

            'vote-mobile*'.$data['unvoteTransaction']->id,
            $data['unvoteTransaction']->id,
            'vote-mobile*'.$data['voteTransaction']->id,
            $data['voteTransaction']->id,
        ]);
});

it('should sort age in ascending order', function () {
    $data = generateTransactions();

    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->call('sortBy', 'age')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            'vote-item*'.$data['voteTransaction']->id,
            $data['voteTransaction']->id,
            'vote-item*'.$data['unvoteTransaction']->id,
            $data['unvoteTransaction']->id,

            'vote-mobile*'.$data['voteTransaction']->id,
            $data['voteTransaction']->id,
            'vote-mobile*'.$data['unvoteTransaction']->id,
            $data['unvoteTransaction']->id,
        ]);
});

it('should sort address in ascending order', function () {
    $data = generateTransactions();

    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->call('sortBy', 'address')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            'vote-item*'.$data['unvoteTransaction']->id,
            $data['unvoteTransaction']->id,
            'vote-item*'.$data['voteTransaction']->id,
            $data['voteTransaction']->id,

            'vote-mobile*'.$data['unvoteTransaction']->id,
            $data['unvoteTransaction']->id,
            'vote-mobile*'.$data['voteTransaction']->id,
            $data['voteTransaction']->id,
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
            'vote-item*'.$data['voteTransaction']->id,
            $data['voteTransaction']->id,
            'vote-item*'.$data['unvoteTransaction']->id,
            $data['unvoteTransaction']->id,

            'vote-mobile*'.$data['voteTransaction']->id,
            $data['voteTransaction']->id,
            'vote-mobile*'.$data['unvoteTransaction']->id,
            $data['unvoteTransaction']->id,
        ]);
});

it('should sort type in ascending order', function () {
    $data = generateTransactions();

    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->call('sortBy', 'type')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            'vote-item*'.$data['voteTransaction']->id,
            $data['voteTransaction']->id,
            'vote-item*'.$data['unvoteTransaction']->id,
            $data['unvoteTransaction']->id,

            'vote-mobile*'.$data['voteTransaction']->id,
            $data['voteTransaction']->id,
            'vote-mobile*'.$data['unvoteTransaction']->id,
            $data['unvoteTransaction']->id,
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
            'vote-item*'.$data['unvoteTransaction']->id,
            $data['unvoteTransaction']->id,
            'vote-item*'.$data['voteTransaction']->id,
            $data['voteTransaction']->id,

            'vote-mobile*'.$data['unvoteTransaction']->id,
            $data['unvoteTransaction']->id,
            'vote-mobile*'.$data['voteTransaction']->id,
            $data['voteTransaction']->id,
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
    Route::get('/test-validators', function () {
        return BladeCompiler::render('<livewire:validators.recent-votes :defer-loading="false" />');
    });

    $data = generateTransactions();

    $this->get('/test-validators?sort=type&sort-direction=asc')
        ->assertSeeInOrder([
            'vote-item*'.$data['voteTransaction']->id,
            $data['voteTransaction']->id,
            'vote-item*'.$data['unvoteTransaction']->id,
            $data['unvoteTransaction']->id,

            'vote-mobile*'.$data['voteTransaction']->id,
            $data['voteTransaction']->id,
            'vote-mobile*'.$data['unvoteTransaction']->id,
            $data['unvoteTransaction']->id,
        ]);

    $this->get('/test-validators?sort=type&sort-direction=desc')
        ->assertSeeInOrder([
            'vote-item*'.$data['unvoteTransaction']->id,
            $data['unvoteTransaction']->id,
            'vote-item*'.$data['voteTransaction']->id,
            $data['voteTransaction']->id,

            'vote-mobile*'.$data['unvoteTransaction']->id,
            $data['unvoteTransaction']->id,
            'vote-mobile*'.$data['voteTransaction']->id,
            $data['voteTransaction']->id,
        ]);
});

it('should force default sort direction if invalid query string value', function () {
    Route::get('/test-validators', function () {
        return BladeCompiler::render('<livewire:validators.recent-votes :defer-loading="false" />');
    });

    $data = generateTransactions();

    $this->get('/test-validators?sort=type&sort-direction=desc')
        ->assertSeeInOrder([
            'vote-item*'.$data['unvoteTransaction']->id,
            $data['unvoteTransaction']->id,
            'vote-item*'.$data['voteTransaction']->id,
            $data['voteTransaction']->id,

            'vote-mobile*'.$data['unvoteTransaction']->id,
            $data['unvoteTransaction']->id,
            'vote-mobile*'.$data['voteTransaction']->id,
            $data['voteTransaction']->id,
        ]);

    $this->get('/test-validators?sort=type&sort-direction=testing')
        ->assertSeeInOrder([
            'vote-item*'.$data['voteTransaction']->id,
            $data['voteTransaction']->id,
            'vote-item*'.$data['unvoteTransaction']->id,
            $data['unvoteTransaction']->id,

            'vote-mobile*'.$data['voteTransaction']->id,
            $data['voteTransaction']->id,
            'vote-mobile*'.$data['unvoteTransaction']->id,
            $data['unvoteTransaction']->id,
        ]);
});

it('should sort name then address in ascending order when missing names', function () {
    $validator1 = new WalletViewModel(Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username' => 'validator-name',
        ],
    ]));

    $validator2 = new WalletViewModel(Wallet::factory()->activeValidator()->create([
        'address' => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
        'attributes' => [
            'username' => null,
        ],
    ]));

    $voteTransaction = Transaction::factory()->vote($validator1->address())->create([
        'timestamp' => Carbon::parse('2023-09-18 03:41:04')->getTimestampMs(),
    ]);

    $unvoteTransaction = Transaction::factory()->unvote()->create([
        'timestamp' => Carbon::parse('2023-09-18 04:41:05')->getTimestampMs(),
    ]);

    $voteTransaction2 = Transaction::factory()->vote($validator2->address())->create([
        'timestamp' => Carbon::parse('2023-09-18 05:41:06')->getTimestampMs(),
    ]);

    $unvoteTransaction2 = Transaction::factory()->unvote()->create([
        'timestamp' => Carbon::parse('2023-09-18 06:41:07')->getTimestampMs(),
    ]);

    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->call('sortBy', 'name')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->assertSeeInOrder([
            // Desktop
            'vote-item*'.$voteTransaction2->id,
            $voteTransaction2->id,
            'Vote',
            $validator2->address(),
            'vote-item*'.$voteTransaction->id,
            $voteTransaction->id,
            'Vote',
            $validator1->username(),
            'vote-item*'.$unvoteTransaction->id,
            'Unvote',
            'vote-item*'.$unvoteTransaction2->id,
            'Unvote',

            // // Mobile
            'vote-mobile*'.$voteTransaction2->id,
            $voteTransaction2->id,
            'Vote',
            $validator2->address(),
            'vote-mobile*'.$voteTransaction->id,
            $voteTransaction->id,
            'Vote',
            $validator1->username(),
            'vote-mobile*'.$unvoteTransaction->id,
            'Unvote',
            'vote-mobile*'.$unvoteTransaction2->id,
            'Unvote',
        ]);
});

it('should sort name then address in descending order when missing names', function () {
    $validator1 = new WalletViewModel(Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username' => 'validator-name',
        ],
    ]));

    $validator2 = new WalletViewModel(Wallet::factory()->activeValidator()->create([
        'address' => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
        'attributes' => [
            'username' => null,
        ],
    ]));

    $voteTransaction = Transaction::factory()->vote($validator1->address())->create([
        'timestamp' => Carbon::parse('2023-09-18 03:41:04')->getTimestampMs(),
    ]);

    $unvoteTransaction = Transaction::factory()->unvote()->create([
        'timestamp' => Carbon::parse('2023-09-18 04:41:05')->getTimestampMs(),
    ]);

    $voteTransaction2 = Transaction::factory()->vote($validator2->address())->create([
        'timestamp' => Carbon::parse('2023-09-18 05:41:06')->getTimestampMs(),
    ]);

    $unvoteTransaction2 = Transaction::factory()->unvote()->create([
        'timestamp' => Carbon::parse('2023-09-18 06:41:07')->getTimestampMs(),
    ]);

    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->call('sortBy', 'name')
        ->call('sortBy', 'name')
        ->assertSet('sortDirection', SortDirection::DESC)
        ->assertSeeInOrder([
            // Desktop
            'vote-item*'.$voteTransaction->id,
            $voteTransaction->id,
            'Vote',
            $validator1->username(),
            'vote-item*'.$voteTransaction2->id,
            $voteTransaction2->id,
            'Vote',
            $validator2->address(),
            'vote-item*'.$unvoteTransaction->id,
            'Unvote',
            'vote-item*'.$unvoteTransaction2->id,
            'Unvote',

            // Mobile
            'vote-mobile*'.$voteTransaction->id,
            $voteTransaction->id,
            'Vote',
            $validator1->username(),
            'vote-mobile*'.$voteTransaction2->id,
            $voteTransaction2->id,
            'Vote',
            $validator2->address(),
            'vote-mobile*'.$unvoteTransaction->id,
            'Unvote',
            'vote-mobile*'.$unvoteTransaction2->id,
            'Unvote',
        ]);
});
