<?php

declare(strict_types=1);

use App\Enums\SortDirection;
use App\Http\Livewire\Validators\Tabs;
use App\Models\Receipt;
use App\Models\Transaction;
use App\Models\Wallet;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;
use function Tests\fakeKnownWallets;

function generateReceipts(): void
{
    foreach (Transaction::all() as $transaction) {
        Receipt::factory()->create([
            'transaction_hash'      => $transaction->hash,
            'status'                => true,
        ]);
    }
}

function generateTransactions(): array
{
    $validator1 = Wallet::factory()->activeValidator()->create([
        'address'    => '0x522CbD1C22529a27ba4BFDBf4b6f037F71b2AC77',
        'attributes' => [
            'username' => 'validator-1',
        ],
    ]);

    $validator2 = Wallet::factory()->activeValidator()->create([
        'address'    => '0x09C94A51cb63b4b70A9Dbf190543c371741D13Fd',
        'attributes' => [
            'username' => 'validator-2',
        ],
    ]);

    $validator3 = Wallet::factory()->activeValidator()->create([
        'address'    => '0x2a74550fC2e741118182B7ab020DC0B7Ed01e1db',
        'attributes' => [
            'username' => 'validator-3',
        ],
    ]);

    $sender1 = Wallet::factory()->create(['address' => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B']);
    $sender2 = Wallet::factory()->create(['address' => '0x38b4a84773bC55e88D07cBFC76444C2A37600084']);

    $voteTransaction = Transaction::factory()
        ->vote($validator1->address)
        ->create([
            'timestamp'      => Carbon::parse('2023-09-18 03:41:04')->getTimestampMs(),
            'from'           => $sender1->address,
        ]);

    $unvoteTransaction = Transaction::factory()
        ->unvote()
        ->create([
            'timestamp'      => Carbon::parse('2023-09-18 04:41:04')->getTimestampMs(),
            'from'           => $sender2->address,
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

beforeEach(fn () => $this->travelTo('2023-09-20 05:41:04'));

it('should render', function () {
    Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
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

    Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->assertSee('Showing 0 results')
        ->call('setRecentVotesReady')
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

    Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->assertSee('Showing 0 results')
        ->call('setRecentVotesReady')
        ->assertSee('Showing 4 results');
});

it('should not defer loading if disabled', function () {
    Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->call('setRecentVotesReady')
        ->assertSet('isReady', true)
        ->assertSet('recentVotesIsReady', true)
        ->assertSee('Showing 0 results');
});

it('should show no results message if no votes', function () {
    Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->call('setRecentVotesReady')
        ->assertSee(trans('tables.recent-votes.no_results.no_results'));
});

it('should toggle all filters when "select all" is selected', function () {
    Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->call('setRecentVotesReady')
        ->assertSet('filters.recent-votes', [
            'vote'   => true,
            'unvote' => true,
        ])
        ->assertSet('selectAllFilters.recent-votes', true)
        ->set('filters.recent-votes.vote', true)
        ->assertSet('selectAllFilters.recent-votes', true)
        ->set('selectAllFilters.recent-votes', false)
        ->assertSet('filters.recent-votes', [
            'vote'   => false,
            'unvote' => false,
        ])
        ->set('selectAllFilters.recent-votes', true)
        ->assertSet('filters.recent-votes', [
            'vote'   => true,
            'unvote' => true,
        ]);
});

it('should toggle "select all" when all filters are selected', function () {
    Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->call('setRecentVotesReady')
        ->assertSet('filters.recent-votes', [
            'vote'   => true,
            'unvote' => true,
        ])
        ->assertSet('selectAllFilters.recent-votes', true)
        ->set('filters.recent-votes.vote', false)
        ->assertSet('selectAllFilters.recent-votes', false)
        ->set('filters.recent-votes.vote', true)
        ->assertSet('selectAllFilters.recent-votes', true);
});

it('should filter vote transactions', function () {
    $sender         = Wallet::factory()->create();
    $validator      = Wallet::factory()->activeValidator()->create();

    Wallet::factory()->activeValidator()->create();

    $vote = Transaction::factory()->vote($validator->address)->create([
        'sender_public_key' => $sender->public_key,
        'timestamp'         => Carbon::now()->subMinute(1)->getTimestampMs(),
    ]);

    $unvote = Transaction::factory()->unvote()->create([
        'sender_public_key' => $sender->public_key,
        'timestamp'         => Carbon::now()->subMinute(1)->getTimestampMs(),
    ]);

    generateReceipts();

    Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->call('setRecentVotesReady')
        ->set('filters.recent-votes.vote', true)
        ->set('filters.recent-votes.unvote', false)
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

    Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->call('setRecentVotesReady')
        ->set('filters.recent-votes.vote', false)
        ->set('filters.recent-votes.unvote', true)
        ->assertSee($unvote->id)
        ->assertDontSee($vote->id);
});

it('should show correct message when no filters are selected', function () {
    Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->call('setRecentVotesReady')
        ->set('filters.recent-votes.vote', false)
        ->set('filters.recent-votes.unvote', false)
        ->assertSee(trans('tables.recent-votes.no_results.no_filters'));
});

it('should show correct message when there are no results', function () {
    Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->call('setRecentVotesReady')
        ->assertSee(trans('tables.recent-votes.no_results.no_results'));
});

it('should sort by age descending by default', function () {
    $data = generateTransactions();

    Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->call('setRecentVotesReady')
        ->assertSet('sortKeys.recent-votes', 'age')
        ->assertSet('sortDirections.recent-votes', SortDirection::DESC)
        ->assertSeeInOrder([
            'vote-item*'.$data['unvoteTransaction']->hash,
            $data['unvoteTransaction']->hash,
            'vote-item*'.$data['voteTransaction']->hash,
            $data['voteTransaction']->hash,

            'vote-mobile*'.$data['unvoteTransaction']->hash,
            $data['unvoteTransaction']->hash,
            'vote-mobile*'.$data['voteTransaction']->hash,
            $data['voteTransaction']->hash,
        ]);
});

it('should sort age in ascending order', function () {
    $data = generateTransactions();

    Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->call('setRecentVotesReady')
        ->call('sortBy', 'age')
        ->assertSet('sortDirections.recent-votes', SortDirection::ASC)
        ->assertSeeInOrder([
            'vote-item*'.$data['voteTransaction']->hash,
            $data['voteTransaction']->hash,
            'vote-item*'.$data['unvoteTransaction']->hash,
            $data['unvoteTransaction']->hash,

            'vote-mobile*'.$data['voteTransaction']->hash,
            $data['voteTransaction']->hash,
            'vote-mobile*'.$data['unvoteTransaction']->hash,
            $data['unvoteTransaction']->hash,
        ]);
});

it('should sort address in ascending order', function () {
    $data = generateTransactions();

    Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->call('setRecentVotesReady')
        ->call('sortBy', 'address')
        ->assertSet('sortDirections.recent-votes', SortDirection::ASC)
        ->assertSeeInOrder([
            'vote-item*'.$data['unvoteTransaction']->hash,
            $data['unvoteTransaction']->hash,
            'vote-item*'.$data['voteTransaction']->hash,
            $data['voteTransaction']->hash,

            'vote-mobile*'.$data['unvoteTransaction']->hash,
            $data['unvoteTransaction']->hash,
            'vote-mobile*'.$data['voteTransaction']->hash,
            $data['voteTransaction']->hash,
        ]);
});

it('should sort address in descending order', function () {
    $data = generateTransactions();

    Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->call('setRecentVotesReady')
        ->call('sortBy', 'address')
        ->call('sortBy', 'address')
        ->assertSet('sortDirections.recent-votes', SortDirection::DESC)
        ->assertSeeInOrder([
            'vote-item*'.$data['voteTransaction']->hash,
            $data['voteTransaction']->hash,
            'vote-item*'.$data['unvoteTransaction']->hash,
            $data['unvoteTransaction']->hash,

            'vote-mobile*'.$data['voteTransaction']->hash,
            $data['voteTransaction']->hash,
            'vote-mobile*'.$data['unvoteTransaction']->hash,
            $data['unvoteTransaction']->hash,
        ]);
});

it('should sort type in ascending order', function () {
    $data = generateTransactions();

    Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->call('setRecentVotesReady')
        ->call('sortBy', 'type')
        ->assertSet('sortDirections.recent-votes', SortDirection::ASC)
        ->assertSeeInOrder([
            'vote-item*'.$data['voteTransaction']->hash,
            $data['voteTransaction']->hash,
            'vote-item*'.$data['unvoteTransaction']->hash,
            $data['unvoteTransaction']->hash,

            'vote-mobile*'.$data['voteTransaction']->hash,
            $data['voteTransaction']->hash,
            'vote-mobile*'.$data['unvoteTransaction']->hash,
            $data['unvoteTransaction']->hash,
        ]);
});

it('should sort type in descending order', function () {
    $data = generateTransactions();

    Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->call('setRecentVotesReady')
        ->call('sortBy', 'type')
        ->call('sortBy', 'type')
        ->assertSet('sortDirections.recent-votes', SortDirection::DESC)
        ->assertSeeInOrder([
            'vote-item*'.$data['unvoteTransaction']->hash,
            $data['unvoteTransaction']->hash,
            'vote-item*'.$data['voteTransaction']->hash,
            $data['voteTransaction']->hash,

            'vote-mobile*'.$data['unvoteTransaction']->hash,
            $data['unvoteTransaction']->hash,
            'vote-mobile*'.$data['voteTransaction']->hash,
            $data['voteTransaction']->hash,
        ]);
});

it('should alternate sorting direction', function () {
    $component = Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->call('setRecentVotesReady')
        ->assertSet('sortKeys.recent-votes', 'age')
        ->assertSet('sortDirections.recent-votes', SortDirection::DESC)
        ->call('sortBy', 'age')
        ->assertSet('sortKeys.recent-votes', 'age')
        ->assertSet('sortDirections.recent-votes', SortDirection::ASC);

    foreach (['name', 'address', 'type'] as $column) {
        $component->call('sortBy', $column)
            ->assertSet('sortKeys.recent-votes', $column)
            ->assertSet('sortDirections.recent-votes', SortDirection::ASC)
            ->call('sortBy', $column)
            ->assertSet('sortKeys.recent-votes', $column)
            ->assertSet('sortDirections.recent-votes', SortDirection::DESC);
    }
});

it('should reset page on sorting change', function () {
    Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->call('setRecentVotesReady')
        ->assertSet('paginators.recent-votes', 1)
        ->assertSet('sortKeys.recent-votes', 'age')
        ->assertSet('sortDirections.recent-votes', SortDirection::DESC)
        ->call('gotoPage', 12)
        ->call('sortBy', 'age')
        ->assertSet('paginators.recent-votes', 1)
        ->assertSet('sortKeys.recent-votes', 'age')
        ->assertSet('sortDirections.recent-votes', SortDirection::ASC)
        ->call('gotoPage', 12)
        ->call('sortBy', 'age')
        ->assertSet('paginators.recent-votes', 1)
        ->assertSet('sortKeys.recent-votes', 'age')
        ->assertSet('sortDirections.recent-votes', SortDirection::DESC);
});

it('should parse sorting direction from query string', function () {
    $data = generateTransactions();

    Route::get('/test-validators', function () {
        return BladeCompiler::render('<livewire:validators.tabs :defer-loading="false" />');
    });

    $this->get('/test-validators?view=recent-votes&sort=name&sort-direction=asc')
        ->assertSeeInOrder([
            'vote-item*'.$data['voteTransaction']->hash,
            $data['voteTransaction']->hash,
            'vote-item*'.$data['unvoteTransaction']->hash,
            $data['unvoteTransaction']->hash,

            'vote-mobile*'.$data['voteTransaction']->hash,
            $data['voteTransaction']->hash,
            'vote-mobile*'.$data['unvoteTransaction']->hash,
            $data['unvoteTransaction']->hash,
        ]);

    $this->get('/test-validators?view=recent-votes&sort=type&sort-direction=desc')
        ->assertSeeInOrder([
            'vote-item*'.$data['unvoteTransaction']->hash,
            $data['unvoteTransaction']->hash,
            'vote-item*'.$data['voteTransaction']->hash,
            $data['voteTransaction']->hash,

            'vote-mobile*'.$data['unvoteTransaction']->hash,
            $data['unvoteTransaction']->hash,
            'vote-mobile*'.$data['voteTransaction']->hash,
            $data['voteTransaction']->hash,
        ]);
});

it('should force default sort direction if invalid query string value', function () {
    $data = generateTransactions();

    Route::get('/test-validators', function () {
        return BladeCompiler::render('<livewire:validators.tabs :defer-loading="false" />');
    });

    $this->get('/test-validators?view=recent-votes&sort=type&sort-direction=desc')
        ->assertSeeInOrder([
            'vote-item*'.$data['unvoteTransaction']->hash,
            $data['unvoteTransaction']->hash,
            'vote-item*'.$data['voteTransaction']->hash,
            $data['voteTransaction']->hash,

            'vote-mobile*'.$data['unvoteTransaction']->hash,
            $data['unvoteTransaction']->hash,
            'vote-mobile*'.$data['voteTransaction']->hash,
            $data['voteTransaction']->hash,
        ]);

    $this->get('/test-validators?view=recent-votes&sort=type&sort-direction=testing')
        ->assertSeeInOrder([
            'vote-item*'.$data['voteTransaction']->hash,
            $data['voteTransaction']->hash,
            'vote-item*'.$data['unvoteTransaction']->hash,
            $data['unvoteTransaction']->hash,

            'vote-mobile*'.$data['voteTransaction']->hash,
            $data['voteTransaction']->hash,
            'vote-mobile*'.$data['unvoteTransaction']->hash,
            $data['unvoteTransaction']->hash,
        ]);
});

it('should validate per-page option from query string', function () {
    $data = generateTransactions();

    Route::get('/test-validators', function () {
        return BladeCompiler::render('<livewire:validators.tabs :defer-loading="false" />');
    });

    // Still show all data since 1 per-page is not a valid option
    $this->get('/test-validators?view=recent-votes&per-page=1')
        ->assertSeeInOrder([
            'vote-item*'.$data['unvoteTransaction']->hash,
            $data['unvoteTransaction']->hash,
            'vote-item*'.$data['voteTransaction']->hash,
            $data['voteTransaction']->hash,

            'vote-mobile*'.$data['unvoteTransaction']->hash,
            $data['unvoteTransaction']->hash,
            'vote-mobile*'.$data['voteTransaction']->hash,
            $data['voteTransaction']->hash,
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
        'transaction_hash'      => $failedTransaction->hash,
        'status'                => false,
    ]);

    Receipt::factory()->create([
        'transaction_hash'      => $successfulTransaction->hash,
        'status'                => true,
    ]);

    Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->call('setRecentVotesReady')
        ->assertDontSee($failedTransaction->hash)
        ->assertSee($successfulTransaction->hash);
});

it('should sort name then address in ascending order when missing names', function () {
    $validator1 = new WalletViewModel(Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username' => 'validator-name',
        ],
    ]));

    $validator2 = new WalletViewModel(Wallet::factory()->activeValidator()->create([
        'address'    => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
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

    generateReceipts();

    Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->call('setRecentVotesReady')
        ->call('sortBy', 'name')
        ->assertSet('sortDirections.recent-votes', SortDirection::ASC)
        ->assertSeeInOrder([
            // Desktop
            'vote-item*'.$voteTransaction2->hash,
            $voteTransaction2->hash,
            'Vote',
            $validator2->address(),
            'vote-item*'.$voteTransaction->hash,
            $voteTransaction->hash,
            'Vote',
            $validator1->username(),
            'vote-item*'.$unvoteTransaction->hash,
            'Unvote',
            'vote-item*'.$unvoteTransaction2->hash,
            'Unvote',

            // // Mobile
            'vote-mobile*'.$voteTransaction2->hash,
            $voteTransaction2->hash,
            'Vote',
            $validator2->address(),
            'vote-mobile*'.$voteTransaction->hash,
            $voteTransaction->hash,
            'Vote',
            $validator1->username(),
            'vote-mobile*'.$unvoteTransaction->hash,
            'Unvote',
            'vote-mobile*'.$unvoteTransaction2->hash,
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
        'address'    => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
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

    generateReceipts();

    Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->call('setRecentVotesReady')
        ->call('sortBy', 'name')
        ->call('sortBy', 'name')
        ->assertSet('sortDirections.recent-votes', SortDirection::DESC)
        ->assertSeeInOrder([
            // Desktop
            'vote-item*'.$voteTransaction->hash,
            $voteTransaction->hash,
            'Vote',
            $validator1->username(),
            'vote-item*'.$voteTransaction2->hash,
            $voteTransaction2->hash,
            'Vote',
            $validator2->address(),
            'vote-item*'.$unvoteTransaction->hash,
            'Unvote',
            'vote-item*'.$unvoteTransaction2->hash,
            'Unvote',

            // Mobile
            'vote-mobile*'.$voteTransaction->hash,
            $voteTransaction->hash,
            'Vote',
            $validator1->username(),
            'vote-mobile*'.$voteTransaction2->hash,
            $voteTransaction2->hash,
            'Vote',
            $validator2->address(),
            'vote-mobile*'.$unvoteTransaction->hash,
            'Unvote',
            'vote-mobile*'.$unvoteTransaction2->hash,
            'Unvote',
        ]);
});

it('should sort known name, then name, then address in ascending order when missing names', function () {
    fakeKnownWallets();

    Config::set('arkscan.networks.development.knownWallets', 'http://some.url');

    $validator1 = new WalletViewModel(Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username' => 'validator-name',
        ],
    ]));

    $validator2 = new WalletViewModel(Wallet::factory()->activeValidator()->create([
        'address'    => '0x2a74550fC2e741118182B7ab020DC0B7Ed01e1db',
        'attributes' => [
            'username' => null,
        ],
    ]));

    $validator3 = new WalletViewModel(Wallet::factory()->activeValidator()->create([
        'address'    => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
        'attributes' => [
            'username' => 'validator-3',
        ],
    ]));

    $voteTransaction1 = Transaction::factory()->vote($validator1->address())->create([
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

    $voteTransaction3 = Transaction::factory()->vote($validator3->address())->create([
        'timestamp' => Carbon::parse('2023-09-18 07:41:06')->getTimestampMs(),
    ]);

    Artisan::call('explorer:cache-known-wallets');

    generateReceipts();

    Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->call('setRecentVotesReady')
        ->call('sortBy', 'name')
        ->assertSet('sortDirections.recent-votes', SortDirection::ASC)
        ->assertSeeInOrder([
            // Desktop
            'vote-item*'.$voteTransaction2->hash,
            $voteTransaction2->hash,
            'Vote',
            $validator2->address(),
            'vote-item*'.$voteTransaction3->hash,
            $voteTransaction3->hash,
            'Vote',
            'ACF Hot Wallet',
            'vote-item*'.$voteTransaction1->hash,
            $voteTransaction1->hash,
            'Vote',
            $validator1->username(),
            'vote-item*'.$unvoteTransaction->hash,
            'Unvote',
            'vote-item*'.$unvoteTransaction2->hash,
            'Unvote',

            // Mobile
            'vote-mobile*'.$voteTransaction2->hash,
            $voteTransaction2->hash,
            'Vote',
            $validator2->address(),
            'vote-mobile*'.$voteTransaction3->hash,
            $voteTransaction3->hash,
            'Vote',
            'ACF Hot Wallet',
            'vote-mobile*'.$voteTransaction1->hash,
            $voteTransaction1->hash,
            'Vote',
            $validator1->username(),
            'vote-mobile*'.$unvoteTransaction->hash,
            'Unvote',
            'vote-mobile*'.$unvoteTransaction2->hash,
            'Unvote',
        ]);
});

it('should sort known name, then name, then address in descending order when missing names', function () {
    fakeKnownWallets();

    Config::set('arkscan.networks.development.knownWallets', 'http://some.url');

    $validator1 = new WalletViewModel(Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'username' => 'validator-name',
        ],
    ]));

    $validator2 = new WalletViewModel(Wallet::factory()->activeValidator()->create([
        'address'    => '0x2a74550fC2e741118182B7ab020DC0B7Ed01e1db',
        'attributes' => [
            'username' => null,
        ],
    ]));

    $validator3 = new WalletViewModel(Wallet::factory()->activeValidator()->create([
        'address'    => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
        'attributes' => [
            'username' => 'validator-3',
        ],
    ]));

    $voteTransaction1 = Transaction::factory()->vote($validator1->address())->create([
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

    $voteTransaction3 = Transaction::factory()->vote($validator3->address())->create([
        'timestamp' => Carbon::parse('2023-09-18 07:41:06')->getTimestampMs(),
    ]);

    Artisan::call('explorer:cache-known-wallets');

    generateReceipts();

    Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->call('setRecentVotesReady')
        ->call('sortBy', 'name')
        ->call('sortBy', 'name')
        ->assertSet('sortDirections.recent-votes', SortDirection::DESC)
        ->assertSeeInOrder([
            // Desktop
            'vote-item*'.$voteTransaction1->hash,
            $voteTransaction1->hash,
            'Vote',
            $validator1->username(),
            'vote-item*'.$voteTransaction3->hash,
            $voteTransaction3->hash,
            'Vote',
            'ACF Hot Wallet',
            'vote-item*'.$voteTransaction2->hash,
            $voteTransaction2->hash,
            'Vote',
            $validator2->address(),
            'vote-item*'.$unvoteTransaction->hash,
            'Unvote',
            'vote-item*'.$unvoteTransaction2->hash,
            'Unvote',

            // Mobile
            'vote-mobile*'.$voteTransaction1->hash,
            $voteTransaction1->hash,
            'Vote',
            $validator1->username(),
            'vote-mobile*'.$voteTransaction3->hash,
            $voteTransaction3->hash,
            'Vote',
            'ACF Hot Wallet',
            'vote-mobile*'.$voteTransaction2->hash,
            $voteTransaction2->hash,
            'Vote',
            $validator2->address(),
            'vote-mobile*'.$unvoteTransaction->hash,
            'Unvote',
            'vote-mobile*'.$unvoteTransaction2->hash,
            'Unvote',
        ]);
});
