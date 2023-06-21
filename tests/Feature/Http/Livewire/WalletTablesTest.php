<?php

declare(strict_types=1);

use App\Http\Livewire\WalletTables;
use App\Models\Wallet;
use App\ViewModels\WalletViewModel;
use Livewire\Livewire;

it('should render all tabs for delegates', function () {
    $wallet = Wallet::factory()->activeDelegate()->create();

    Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->assertSeeHtml("tab === 'transactions'")
        ->assertSeeHtml("tab === 'voters'");
});

it('should not render tabs for non-delegates', function () {
    $wallet = Wallet::factory()->create([
        'attributes' => [],
    ]);

    Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->assertSeeHtml("tab === 'transactions'")
        ->assertDontSeeHtml("tab === 'voters'");
});

it('should change view with event', function () {
    $wallet = Wallet::factory()->activeDelegate()->create();

    Livewire::test(WalletTables::class, [new WalletViewModel($wallet)])
        ->assertSet('view', 'transactions')
        ->emit('showWalletView', 'voters')
        ->assertSet('view', 'voters')
        ->emit('showWalletView', 'blocks')
        ->assertSet('view', 'blocks')
        ->emit('showWalletView', 'transactions')
        ->assertSet('view', 'transactions');
});

it('should change view with event', function () {
    Livewire::test(WalletTables::class, [new WalletViewModel($this->subject)])
        ->assertSet('view', 'transactions')
        ->emit('showWalletView', 'voters')
        ->assertSet('view', 'voters')
        ->emit('showWalletView', 'blocks')
        ->assertSet('view', 'blocks')
        ->emit('showWalletView', 'transactions')
        ->assertSet('view', 'transactions');
});
