<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\TopAccountsTable;
use App\Models\Scopes\OrderByBalanceScope;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;

it('should list the first page of records', function () {
    (new NetworkCache())->setSupply(fn () => strval(10e8));

    Wallet::factory(25)->create();

    $component = Livewire::test(TopAccountsTable::class);

    foreach (ViewModelFactory::paginate(Wallet::withScope(OrderByBalanceScope::class)->paginate())->items() as $wallet) {
        $component->assertSee($wallet->address());
        $component->assertSee(NumberFormatter::currency($wallet->balance(), Network::currency()));
    }
});

it('should change per page', function () {
    (new NetworkCache())->setSupply(fn () => strval(10e8));

    $visibleWallets    = Wallet::factory(10)->create(['balance' => 1000]);
    $notVisibleWallets = Wallet::factory(10)->create(['balance' => 10]);

    $component = Livewire::test(TopAccountsTable::class)
        ->set('perPage', 50);

    foreach ($visibleWallets->concat($notVisibleWallets) as $wallet) {
        $component->assertSee($wallet->address);
    }

    $component->call('setPerPage', 10);

    foreach ($visibleWallets as $wallet) {
        $component->assertSee($wallet->address);
    }

    foreach ($notVisibleWallets as $wallet) {
        $component->assertDontSee($wallet->address);
    }
});

it('should not per page if not a valid option', function () {
    (new NetworkCache())->setSupply(fn () => strval(10e8));

    $visibleWallets    = Wallet::factory(10)->create(['balance' => 1000]);
    $notVisibleWallets = Wallet::factory(8)->create(['balance' => 10]);

    $component = Livewire::test(TopAccountsTable::class)
        ->set('perPage', 10);

    foreach ($visibleWallets as $wallet) {
        $component->assertSee($wallet->address);
    }

    foreach ($notVisibleWallets as $wallet) {
        $component->assertDontSee($wallet->address);
    }

    $component->call('setPerPage', 18)
        ->assertSet('perPage', 10);

    foreach ($visibleWallets as $wallet) {
        $component->assertSee($wallet->address);
    }

    foreach ($notVisibleWallets as $wallet) {
        $component->assertDontSee($wallet->address);
    }
});

it('should go to page 1 when changing per page', function () {
    (new NetworkCache())->setSupply(fn () => strval(10e8));

    Wallet::factory(100)->create();

    $component = Livewire::test(TopAccountsTable::class)
        ->call('gotoPage', 2)
        ->assertSet('page', 2)
        ->call('setPerPage', 25)
        ->assertSet('page', 1);
});
