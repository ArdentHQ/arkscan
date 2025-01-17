<?php

declare(strict_types=1);

use App\Enums\SortDirection;
use App\Http\Livewire\Validators\RecentVotes;
use App\Models\Receipt;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;

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

    generateReceipts();

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

    generateReceipts();

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

    generateReceipts();

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

    generateReceipts();

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

function generateReceipts(): void
{
    foreach (Transaction::all() as $transaction) {
        Receipt::factory()->create([
            'id'      => $transaction->id,
            'success' => true,
        ]);
    }
}

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
        'timestamp' => Timestamp::fromUnix(Carbon::parse('2023-09-18 03:41:04')->unix())->unix(),
    ]);

    $unvoteTransaction = Transaction::factory()->unvote()->create([
        'timestamp' => Timestamp::fromUnix(Carbon::parse('2023-09-18 04:41:04')->unix())->unix(),
    ]);

    generateReceipts();

    return [
        'validator1'           => $validator1,
        'validator2'           => $validator2,
        'validator3'           => $validator3,
        'voteTransaction'      => $voteTransaction,
        'unvoteTransaction'    => $unvoteTransaction,
    ];
};

it('should sort by age descending by default', function () {
    $data = generateTransactions();

    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->assertSet('sortKey', 'age')
        ->assertSet('sortDirection', SortDirection::DESC)
        ->assertSeeInOrder([
            $data['unvoteTransaction']->address,
            $data['voteTransaction']->address,
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
            $data['voteTransaction']->address,
            $data['unvoteTransaction']->address,
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
            $data['voteTransaction']->address,
            $data['unvoteTransaction']->address,
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
            $data['unvoteTransaction']->address,
            $data['voteTransaction']->address,
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
            $data['unvoteTransaction']->address,
            $data['voteTransaction']->address,
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
            $data['voteTransaction']->address,
            $data['unvoteTransaction']->address,
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
            $data['unvoteTransaction']->address,
            $data['voteTransaction']->address,
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
            $data['voteTransaction']->address,
            $data['unvoteTransaction']->address,
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
    Route::get('/test-validators', function () {
        return BladeCompiler::render('<livewire:validators.recent-votes :defer-loading="false" />');
    });

    $data = generateTransactions();

    $this->get('/test-validators?sort=type&sort-direction=asc')
        ->assertSeeInOrder([
            $data['unvoteTransaction']->address,
            $data['voteTransaction']->address,
            $data['unvoteTransaction']->address,
            $data['voteTransaction']->address,
        ]);

    $this->get('/test-validators?sort=type&sort-direction=desc')
        ->assertSeeInOrder([
            $data['voteTransaction']->address,
            $data['unvoteTransaction']->address,
            $data['voteTransaction']->address,
            $data['unvoteTransaction']->address,
        ]);
});

it('should force default sort direction if invalid query string value', function () {
    Route::get('/test-validators', function () {
        return BladeCompiler::render('<livewire:validators.recent-votes :defer-loading="false" />');
    });

    $data = generateTransactions();

    $this->get('/test-validators?sort=type&sort-direction=desc')
        ->assertSeeInOrder([
            $data['voteTransaction']->address,
            $data['unvoteTransaction']->address,
            $data['voteTransaction']->address,
            $data['unvoteTransaction']->address,
        ]);

    $this->get('/test-validators?sort=type&sort-direction=testing')
        ->assertSeeInOrder([
            $data['unvoteTransaction']->address,
            $data['voteTransaction']->address,
            $data['unvoteTransaction']->address,
            $data['voteTransaction']->address,
        ]);
});

it('should not show failed transactions', function () {
    $validator = Wallet::factory()->activeValidator()->create();

    $failedTransaction = Transaction::factory()->vote($validator->address)->create([
        'timestamp' => Carbon::now()->subMinute(2)->getTimestampMs(),
    ]);

    $successfulTransaction = Transaction::factory()->vote($validator->address)->create([
        'timestamp' => Carbon::now()->subMinute(1)->getTimestampMs(),
    ]);

    Receipt::factory()->create([
        'id'      => $failedTransaction->id,
        'success' => false,
    ]);

    Receipt::factory()->create([
        'id'      => $successfulTransaction->id,
        'success' => true,
    ]);

    Livewire::test(RecentVotes::class)
        ->call('setIsReady')
        ->assertDontSee($failedTransaction->id)
        ->assertSee($successfulTransaction->id);
});
