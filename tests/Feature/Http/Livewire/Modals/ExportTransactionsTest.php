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
    $wallet = new WalletViewModel(Wallet::factory()->activeDelegate()->create());

    Transaction::factory()->create([
        'sender_public_key' => $wallet->publicKey(),
    ]);

    Livewire::test(ExportTransactions::class, [$wallet])
        ->assertSet('hasTransactions', false)
        ->call('setIsReady')
        ->assertSet('hasTransactions', true);

    $wallet = new WalletViewModel(Wallet::factory()->activeDelegate()->create());

    Transaction::factory()->create([
        'recipient_id' => $wallet->address(),
    ]);

    Livewire::test(ExportTransactions::class, [$wallet])
        ->assertSet('hasTransactions', false)
        ->call('setIsReady')
        ->assertSet('hasTransactions', true);
});

it('should not be enabled if no transactions', function () {
    $wallet = new WalletViewModel(Wallet::factory()->activeDelegate()->create());

    Livewire::test(ExportTransactions::class, [$wallet])
        ->assertSet('hasTransactions', false)
        ->call('setIsReady')
        ->assertSet('hasTransactions', false);
});

it('should set the oldest transaction timestamp of the wallet', function () {
    $wallet = Wallet::factory()->create();

    Transaction::factory()->transfer()->create([
        'sender_public_key' => $wallet->public_key,
        'timestamp'         => 3000,
    ]);

    Transaction::factory()->transfer()->create([
        'recipient_id' => $wallet->address,
        'timestamp'    => 2000,
    ]);

    Transaction::factory()->transfer()->create([
        'timestamp' => 1000,
    ]);

    Livewire::test(ExportTransactions::class, [new WalletViewModel($wallet)])
        ->assertSet('oldestTimestamp', null)
        ->call('setIsReady')
        ->assertSet('oldestTimestamp', 2000)
        ->assertSet('hasTransactions', true);
});

it('should take multipayments received by the wallet into account', function () {
    $wallet = Wallet::factory()->create();

    Transaction::factory()->transfer()->create([
        'sender_public_key' => $wallet->public_key,
        'timestamp'         => 3000,
    ]);

    Transaction::factory()->multiPayment()->create([
        'timestamp' => 1500,
        'asset'     => [
            'payments' => [
                ['amount' => 1, 'recipientId' => 'another-address'],
                ['amount' => 1, 'recipientId' => $wallet->address],
            ],
        ],
    ]);

    Transaction::factory()->multiPayment()->create([
        'timestamp' => 500,
        'asset'     => [
            'payments' => [
                ['amount' => 1, 'recipientId' => 'another-address'],
            ],
        ],
    ]);

    Livewire::test(ExportTransactions::class, [new WalletViewModel($wallet)])
        ->call('setIsReady')
        ->assertSet('oldestTimestamp', 1500)
        ->assertSet('hasTransactions', true);
});

it('should be enabled if the wallet only received multipayments', function () {
    $wallet = Wallet::factory()->create(['public_key' => null]);

    Transaction::factory()->multiPayment()->create([
        'timestamp' => 1500,
        'asset'     => [
            'payments' => [
                ['amount' => 1, 'recipientId' => $wallet->address],
            ],
        ],
    ]);

    Livewire::test(ExportTransactions::class, [new WalletViewModel($wallet)])
        ->call('setIsReady')
        ->assertSet('oldestTimestamp', 1500)
        ->assertSet('hasTransactions', true);
});

it('should not set an oldest timestamp if the wallet has no transactions', function () {
    $wallet = Wallet::factory()->create(['public_key' => null]);

    Transaction::factory()->transfer()->create(['timestamp' => 1000]);

    Livewire::test(ExportTransactions::class, [new WalletViewModel($wallet)])
        ->call('setIsReady')
        ->assertSet('oldestTimestamp', null)
        ->assertSet('hasTransactions', false);
});

it('should pass the oldest timestamp to the export', function () {
    $wallet = Wallet::factory()->create();

    Transaction::factory()->transfer()->create([
        'sender_public_key' => $wallet->public_key,
        'timestamp'         => 1234,
    ]);

    Livewire::test(ExportTransactions::class, [new WalletViewModel($wallet)])
        ->call('setIsReady')
        ->call('openModal')
        ->assertSeeHtml('oldestTimestamp: 1234,');
});
