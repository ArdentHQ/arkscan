<?php

declare(strict_types=1);

use App\Models\Transaction;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    Cache::tags('statistics')->flush();
});

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    $this
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home/Index')
            ->where('baseUrl', route('home', absolute: false))
            ->missing('transactions'));
});

it('should show the no-results message when no transactions exist', function () {
    $this
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home/Index')
            ->reloadOnly('transactions', fn (Assert $reload) => $reload
                ->has('transactions.data', 0)
                ->has('transactions.meta')
                ->where('transactions.noResultsMessage', (string) trans('tables.transactions.no_results.no_results'))));
});

it('should return transactions without a no-results message', function () {
    $transaction = Transaction::factory()->create();

    $this
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home/Index')
            ->reloadOnly('transactions', fn (Assert $reload) => $reload
                ->has('transactions.data', 1)
                ->has('transactions.meta')
                ->where('transactions.data.0.hash', $transaction->hash)
                ->where('transactions.noResultsMessage', null)));
});
