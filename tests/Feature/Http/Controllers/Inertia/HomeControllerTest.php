<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\MainsailCache;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\Cache\ValidatorCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
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
            ->has('statistics')
            ->missing('transactions')
            ->missing('blocks'));
});

it('should have statistics', function () {
    Config::set('arkscan.networks.development.canBeExchanged', false);

    Wallet::factory()->count(11)->create();

    $cache = new NetworkCache();

    $cache->setSupply(function (): float {
        return 12345.6789 * 1e18;
    });

    $cache->setVotesPercentage('123.45');

    (new ValidatorCache())->setTotalBalanceVoted(4567.2345);
    (new MainsailCache())->setFees([
        'min' => '1500000000',
        'avg' => '2500000000',
        'max' => '3500000000',
    ]);

    $this
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home/Index')
            ->where('statistics.addresses', '11')
            ->where('statistics.totalSupply', '12K')
            ->where('statistics.voting.percentage', '123.45%')
            ->where('statistics.voting.amount', '4K')
            ->where('statistics.gas.low.amount', '1.5')
            ->where('statistics.gas.low.value', null)
            ->where('statistics.gas.average.amount', '2.5')
            ->where('statistics.gas.average.value', null)
            ->where('statistics.gas.high.amount', '3.5')
            ->where('statistics.gas.high.value', null));
});

it('should calculate gas statistics with value', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);

    Wallet::factory()->count(11)->create();

    $cache = new NetworkCache();

    $cache->setSupply(function (): float {
        return 12345.6789 * 1e18;
    });

    $cache->setVotesPercentage('123.45');

    (new ValidatorCache())->setTotalBalanceVoted(4567.2345);
    (new MainsailCache())->setFees([
        'min' => (string) BigNumber::new(1.5 * 1e18),
        'avg' => (string) BigNumber::new(2.5 * 1e18),
        'max' => (string) BigNumber::new(3.5 * 1e18),
    ]);

    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 2.0);

    $this
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home/Index')
            ->where('statistics.gas.low.amount', '1500000000')
            ->where('statistics.gas.low.value', '$3.00')
            ->where('statistics.gas.average.amount', '2500000000')
            ->where('statistics.gas.average.value', '$5.00')
            ->where('statistics.gas.high.amount', '3500000000')
            ->where('statistics.gas.high.value', '$7.00'));
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

it('should show the no-results message when no blocks exist', function () {
    $this
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home/Index')
            ->reloadOnly('blocks', fn (Assert $reload) => $reload
                ->has('blocks.data', 0)
                ->has('blocks.meta')
                ->where('blocks.noResultsMessage', (string) trans('tables.blocks.no_results'))));
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

it('should return blocks without a no-results message', function () {
    $block = Block::factory()->create();

    $this
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home/Index')
            ->reloadOnly('blocks', fn (Assert $reload) => $reload
                ->has('blocks.data', 1)
                ->has('blocks.meta')
                ->where('blocks.data.0.hash', $block->hash)
                ->where('blocks.noResultsMessage', null)));
});
