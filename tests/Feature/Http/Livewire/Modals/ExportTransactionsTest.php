<?php

declare(strict_types=1);

use App\Http\Livewire\Modals\ExportTransactions;
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
