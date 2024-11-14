<?php

declare(strict_types=1);

use App\Http\Livewire\Modals\ExportBlocks;
use App\Models\Block;
use App\Models\Wallet;
use App\ViewModels\WalletViewModel;
use Livewire\Livewire;

it('should render', function () {
    $wallet = new WalletViewModel(Wallet::factory()->activeValidator()->create());

    Livewire::test(ExportBlocks::class, [$wallet])
        ->assertSet('publicKey', $wallet->publicKey())
        ->assertSee(trans('actions.export'))
        ->assertDontSee(trans('pages.wallet.export-blocks-modal.title'));
});

it('should handle non-validators', function () {
    $wallet = new WalletViewModel(Wallet::factory()->create(['attributes' => []]));

    Livewire::test(ExportBlocks::class, [$wallet])
        ->assertSet('publicKey', $wallet->publicKey())
        ->assertSee(trans('actions.export'))
        ->assertDontSee(trans('pages.wallet.export-blocks-modal.title'));
});

it('should open modal', function () {
    $wallet = new WalletViewModel(Wallet::factory()->activeValidator()->create());

    Livewire::test(ExportBlocks::class, [$wallet])
        ->assertSet('publicKey', $wallet->publicKey())
        ->assertSee(trans('actions.export'))
        ->assertDontSee(trans('pages.wallet.export-blocks-modal.title'))
        ->call('openModal')
        ->assertSee(trans('pages.wallet.export-blocks-modal.title'));
});

it('should close modal', function () {
    $wallet = new WalletViewModel(Wallet::factory()->activeValidator()->create());

    Livewire::test(ExportBlocks::class, [$wallet])
        ->assertSet('publicKey', $wallet->publicKey())
        ->assertSee(trans('actions.export'))
        ->call('openModal')
        ->assertSee(trans('pages.wallet.export-blocks-modal.title'))
        ->call('closeModal')
        ->assertDontSee(trans('pages.wallet.export-blocks-modal.title'));
});

it('should not be enabled if not ready', function () {
    $wallet = new WalletViewModel(Wallet::factory()->activeValidator()->create());

    Block::factory()->create([
        'generator_address' => $wallet->address(),
    ]);

    Livewire::test(ExportBlocks::class, [$wallet])
        ->assertSet('hasForgedBlocks', false)
        ->call('setIsReady')
        ->assertSet('hasForgedBlocks', true);
});

it('should not be enabled if no blocks', function () {
    $wallet = new WalletViewModel(Wallet::factory()->activeValidator()->create());

    Livewire::test(ExportBlocks::class, [$wallet])
        ->assertSet('hasForgedBlocks', false)
        ->call('setIsReady')
        ->assertSet('hasForgedBlocks', false);
});
