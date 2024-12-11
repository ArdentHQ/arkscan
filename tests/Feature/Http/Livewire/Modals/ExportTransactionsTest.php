<?php

declare(strict_types=1);

use App\Http\Livewire\Modals\ExportTransactions;
use App\Models\Transaction;
use App\Models\Wallet;
use App\ViewModels\WalletViewModel;
use Livewire\Livewire;

it('should render', function () {
    $wallet = Wallet::factory()->create();

    Livewire::test(ExportTransactions::class, [new WalletViewModel($wallet)])
        ->assertSet('address', $wallet->address)
        ->assertSee(trans('actions.export'))
        ->assertDontSee(trans('pages.wallet.export-transactions-modal.title'));
});

it('should open modal', function () {
    $wallet = Wallet::factory()->create();

    Livewire::test(ExportTransactions::class, [new WalletViewModel($wallet)])
        ->assertSet('address', $wallet->address)
        ->assertSee(trans('actions.export'))
        ->assertDontSee(trans('pages.wallet.export-transactions-modal.title'))
        ->call('openModal')
        ->assertSee(trans('pages.wallet.export-transactions-modal.title'));
});

it('should close modal', function () {
    $wallet = Wallet::factory()->create();

    Livewire::test(ExportTransactions::class, [new WalletViewModel($wallet)])
        ->assertSet('address', $wallet->address)
        ->assertSee(trans('actions.export'))
        ->call('openModal')
        ->assertSee(trans('pages.wallet.export-transactions-modal.title'))
        ->call('closeModal')
        ->assertDontSee(trans('pages.wallet.export-transactions-modal.title'));
});

it('should not be enabled if not ready', function () {
    $wallet = new WalletViewModel(Wallet::factory()->activeValidator()->create());

    Transaction::factory()->create([
        'sender_public_key' => $wallet->publicKey(),
    ]);

    Livewire::test(ExportTransactions::class, [$wallet])
        ->assertSet('hasTransactions', false)
        ->call('setIsReady')
        ->assertSet('hasTransactions', true);

    $wallet = new WalletViewModel(Wallet::factory()->activeValidator()->create());

    Transaction::factory()->create([
        'recipient_address' => $wallet->address(),
    ]);

    Livewire::test(ExportTransactions::class, [$wallet])
        ->assertSet('hasTransactions', false)
        ->call('setIsReady')
        ->assertSet('hasTransactions', true);
});

it('should not be enabled if no transactions', function () {
    $wallet = new WalletViewModel(Wallet::factory()->activeValidator()->create());

    Livewire::test(ExportTransactions::class, [$wallet])
        ->assertSet('hasTransactions', false)
        ->call('setIsReady')
        ->assertSet('hasTransactions', false);
});
