<?php

declare(strict_types=1);

use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use Inertia\Testing\AssertableInertia as Assert;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    $this
        ->get(route('top-accounts'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('TopAccounts/List'));
});

it('should return wallets ordered by balance with computed fields', function () {
    $this->withoutExceptionHandling();

    (new NetworkCache())->setSupply(fn () => strval(10 * 1e18));

    $highestBalanceWallet = Wallet::factory()->create(['balance' => 9 * 1e18]);
    $lowestBalanceWallet  = Wallet::factory()->create(['balance' => 1 * 1e18]);

    $this
        ->get(route('top-accounts'), [
            'X-Inertia-Partial-Component' => 'TopAccounts/List',
            'X-Inertia-Partial-Data'      => 'wallets',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('TopAccounts/List')
            ->has('wallets.data', 2)
            ->where('wallets.data.0.address', $highestBalanceWallet->address)
            ->where('wallets.data.1.address', $lowestBalanceWallet->address)
            ->where('wallets.data.0.balancePercentage', fn ($value) => abs($value - 90) < 0.0001));
});

it('should return a no results message when no wallets exist', function () {
    $this->withoutExceptionHandling();

    $this
        ->get(route('top-accounts'), [
            'X-Inertia-Partial-Component' => 'TopAccounts/List',
            'X-Inertia-Partial-Data'      => 'wallets',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('TopAccounts/List')
            ->has('wallets.data', 0)
            ->where('wallets.noResultsMessage', (string) trans('tables.wallets.top_accounts_no_results')));
});
