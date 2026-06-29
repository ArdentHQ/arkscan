<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\WalletCache;
use Inertia\Testing\AssertableInertia as Assert;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    $block = Block::factory()->create();

    $this
        ->get(route('block', $block->hash))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Block/Show')
            ->has('block')
            ->where('block.hash', $block->hash)
            ->where('block.number', $block->number->toNumber())
            ->where('block.transactionCount', $block->transactions_count)
            ->missing('transactions'));
});

it('should return block details', function () {
    $this->withoutExceptionHandling();

    (new NetworkCache())->setHeight(fn () => 1000);

    $validator = Wallet::factory()->create();

    $block = Block::factory()->create([
        'proposer'           => $validator->address,
        'number'             => 500,
        'transactions_count' => 0,
    ]);

    $this
        ->get(route('block', $block->hash))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Block/Show')
            ->where('block.hash', $block->hash)
            ->where('block.number', 500)
            ->where('block.transactionCount', 0)
            ->where('block.proposer.address', $validator->address)
            ->where('block.confirmations', 500));
});

it('should return block with validator username', function () {
    $this->withoutExceptionHandling();

    $validator = Wallet::factory()->create([
        'attributes' => ['username' => 'test_validator'],
    ]);

    (new WalletCache())->setWalletNameByAddress($validator->address, 'test_validator');

    $block = Block::factory()->create([
        'proposer' => $validator->address,
    ]);

    $this
        ->get(route('block', $block->hash))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Block/Show')
            ->where('block.proposer.address', $validator->address)
            ->where('block.proposer.username', 'test_validator')
            ->where('block.proposer.hasUsername', true));
});

it('should show no-results message when no transactions exist', function () {
    $block = Block::factory()->create([
        'transactions_count' => 0,
    ]);

    $this
        ->get(route('block', $block->hash), [
            'X-Inertia-Partial-Component' => 'Block/Show',
            'X-Inertia-Partial-Data'      => 'transactions',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Block/Show')
            ->has('transactions.data', 0)
            ->has('transactions.meta')
            ->where('transactions.noResultsMessage', trans('tables.transactions.no_results.no_results')));
});

it('should return transactions without no-results message', function () {
    $block = Block::factory()->create([
        'transactions_count' => 1,
    ]);

    $transaction = Transaction::factory()->create([
        'block_hash'   => $block->hash,
        'block_number' => $block->number,
    ]);

    $this
        ->get(route('block', $block->hash), [
            'X-Inertia-Partial-Component' => 'Block/Show',
            'X-Inertia-Partial-Data'      => 'transactions',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Block/Show')
            ->has('transactions.data', 1)
            ->has('transactions.meta')
            ->where('transactions.data.0.hash', $transaction->hash)
            ->where('transactions.noResultsMessage', null));
});

it('should paginate transactions', function () {
    $block = Block::factory()->create([
        'transactions_count' => 25,
    ]);

    Transaction::factory()->count(25)->create([
        'block_hash'   => $block->hash,
        'block_number' => $block->number,
    ]);

    $this
        ->get(route('block', $block->hash), [
            'X-Inertia-Partial-Component' => 'Block/Show',
            'X-Inertia-Partial-Data'      => 'transactions',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Block/Show')
            ->has('transactions.meta')
            ->where('transactions.total', 25));
});

it('should include token approval details for approve transactions in block', function () {
    $spender = Wallet::factory()->create();

    $block = Block::factory()->create(['transactions_count' => 1]);

    Transaction::factory()
        ->approve($spender->address, BigNumber::new(5000))
        ->create([
            'block_hash'   => $block->hash,
            'block_number' => $block->number,
            'status'       => true,
        ]);

    $this
        ->get(route('block', $block->hash), [
            'X-Inertia-Partial-Component' => 'Block/Show',
            'X-Inertia-Partial-Data'      => 'transactions',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Block/Show')
            ->has('transactions.data', 1)
            ->where('transactions.data.0.tokenApprovalDetails.spender.address', $spender->address));
});
