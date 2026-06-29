<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use Inertia\Testing\AssertableInertia as Assert;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    $this->get(route('bookmarks'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Bookmarks/Index'));
});

it('should return empty addresses when no bookmark IDs are provided', function () {
    $this->withoutExceptionHandling();

    $this->get(route('bookmarks'), [
        'X-Inertia-Partial-Component' => 'Bookmarks/Index',
        'X-Inertia-Partial-Data'      => 'addresses',
        'X-Bookmarks'                 => json_encode(['addresses' => []]),
    ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Bookmarks/Index')
            ->has('addresses.data', 0)
            ->where('addresses.noResultsMessage', (string) trans('tables.bookmarks.addresses.no_results')));
});

it('should return bookmarked addresses', function () {
    $this->withoutExceptionHandling();

    $wallet1 = Wallet::factory()->create();
    $wallet2 = Wallet::factory()->create();

    $this->get(route('bookmarks'), [
        'X-Inertia-Partial-Component' => 'Bookmarks/Index',
        'X-Inertia-Partial-Data'      => 'addresses',
        'X-Bookmarks'                 => json_encode(['addresses' => [$wallet1->address, $wallet2->address]]),
    ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Bookmarks/Index')
            ->has('addresses.data', 2)
            ->where('addresses.noResultsMessage', null));
});

it('should return empty transactions when no bookmark IDs are provided', function () {
    $this->withoutExceptionHandling();

    $this->get(route('bookmarks'), [
        'X-Inertia-Partial-Component' => 'Bookmarks/Index',
        'X-Inertia-Partial-Data'      => 'transactions',
        'X-Bookmarks'                 => json_encode(['transactions' => []]),
    ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Bookmarks/Index')
            ->has('transactions.data', 0)
            ->where('transactions.noResultsMessage', (string) trans('tables.bookmarks.transactions.no_results')));
});

it('should return bookmarked transactions', function () {
    $this->withoutExceptionHandling();

    $transaction = Transaction::factory()->transfer()->create();
    Wallet::factory()->create(['address' => $transaction->from]);
    Wallet::factory()->create(['address' => $transaction->to]);

    $this->get(route('bookmarks'), [
        'X-Inertia-Partial-Component' => 'Bookmarks/Index',
        'X-Inertia-Partial-Data'      => 'transactions',
        'X-Bookmarks'                 => json_encode(['transactions' => [$transaction->hash]]),
    ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Bookmarks/Index')
            ->has('transactions.data', 1)
            ->where('transactions.noResultsMessage', null));
});

it('should return empty blocks when no bookmark IDs are provided', function () {
    $this->withoutExceptionHandling();

    $this->get(route('bookmarks'), [
        'X-Inertia-Partial-Component' => 'Bookmarks/Index',
        'X-Inertia-Partial-Data'      => 'blocks',
        'X-Bookmarks'                 => json_encode(['blocks' => []]),
    ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Bookmarks/Index')
            ->has('blocks.data', 0)
            ->where('blocks.noResultsMessage', (string) trans('tables.bookmarks.blocks.no_results')));
});

it('should return bookmarked blocks', function () {
    $this->withoutExceptionHandling();

    $block = Block::factory()->create();

    $this->get(route('bookmarks'), [
        'X-Inertia-Partial-Component' => 'Bookmarks/Index',
        'X-Inertia-Partial-Data'      => 'blocks',
        'X-Bookmarks'                 => json_encode(['blocks' => [$block->hash]]),
    ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Bookmarks/Index')
            ->has('blocks.data', 1)
            ->where('blocks.noResultsMessage', null));
});

it('should return empty result when no X-Bookmarks header is provided', function () {
    $this->withoutExceptionHandling();

    $this->get(route('bookmarks'), [
        'X-Inertia-Partial-Component' => 'Bookmarks/Index',
        'X-Inertia-Partial-Data'      => 'addresses',
    ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Bookmarks/Index')
            ->has('addresses.data', 0)
            ->where('addresses.noResultsMessage', (string) trans('tables.bookmarks.addresses.no_results')));
});

it('should include token approval details for bookmarked approve transaction', function () {
    $this->withoutExceptionHandling();

    $spender = Wallet::factory()->create();

    $transaction = Transaction::factory()
        ->approve($spender->address, BigNumber::new(5000))
        ->create(['status' => true]);

    $this->get(route('bookmarks'), [
        'X-Inertia-Partial-Component' => 'Bookmarks/Index',
        'X-Inertia-Partial-Data'      => 'transactions',
        'X-Bookmarks'                 => json_encode(['transactions' => [$transaction->hash]]),
    ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Bookmarks/Index')
            ->has('transactions.data', 1)
            ->where('transactions.data.0.tokenApprovalDetails.spender.address', $spender->address));
});
