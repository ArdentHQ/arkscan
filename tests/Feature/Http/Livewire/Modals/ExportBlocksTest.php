<?php

declare(strict_types=1);

use App\Http\Livewire\Modals\ExportBlocks;
use App\Models\Block;
use App\Models\Wallet;
use App\ViewModels\WalletViewModel;
use Livewire\Livewire;

it('should render', function () {
    $wallet = new WalletViewModel(Wallet::factory()->activeDelegate()->create());

    Livewire::test(ExportBlocks::class, [$wallet])
        ->assertSet('publicKey', $wallet->publicKey())
        ->assertSet('username', $wallet->username())
        ->assertSee(trans('actions.export'))
        ->assertDontSee(trans('pages.wallet.export-blocks-modal.title'));
});

it('should handle non-delegates', function () {
    $wallet = new WalletViewModel(Wallet::factory()->create(['attributes' => []]));

    Livewire::test(ExportBlocks::class, [$wallet])
        ->assertSet('publicKey', $wallet->publicKey())
        ->assertSet('username', null)
        ->assertSee(trans('actions.export'))
        ->assertDontSee(trans('pages.wallet.export-blocks-modal.title'));
});

it('should open modal', function () {
    $wallet = new WalletViewModel(Wallet::factory()->activeDelegate()->create());

    Livewire::test(ExportBlocks::class, [$wallet])
        ->assertSet('publicKey', $wallet->publicKey())
        ->assertSet('username', $wallet->username())
        ->assertSee(trans('actions.export'))
        ->assertDontSee(trans('pages.wallet.export-blocks-modal.title'))
        ->call('openModal')
        ->assertSee(trans('pages.wallet.export-blocks-modal.title'));
});

it('should close modal', function () {
    $wallet = new WalletViewModel(Wallet::factory()->activeDelegate()->create());

    Livewire::test(ExportBlocks::class, [$wallet])
        ->assertSet('publicKey', $wallet->publicKey())
        ->assertSet('username', $wallet->username())
        ->assertSee(trans('actions.export'))
        ->call('openModal')
        ->assertSee(trans('pages.wallet.export-blocks-modal.title'))
        ->call('closeModal')
        ->assertDontSee(trans('pages.wallet.export-blocks-modal.title'));
});

it('should not be enabled if not ready', function () {
    $wallet = new WalletViewModel(Wallet::factory()->activeDelegate()->create());

    Block::factory()->create([
        'generator_public_key' => $wallet->publicKey(),
    ]);

    Livewire::test(ExportBlocks::class, [$wallet])
        ->assertSet('hasForgedBlocks', false)
        ->call('setIsReady')
        ->assertSet('hasForgedBlocks', true);
});
