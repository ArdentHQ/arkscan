<?php

declare(strict_types=1);

use App\Http\Livewire\Modals\ExportTransactions;
use Livewire\Livewire;

it('should render', function () {
    Livewire::test(ExportTransactions::class)
        ->assertSee(trans('actions.export'))
        ->assertDontSee(trans('pages.wallet.export-transactions-modal.title'));
});

it('should open modal', function () {
    Livewire::test(ExportTransactions::class)
        ->assertSee(trans('actions.export'))
        ->assertDontSee(trans('pages.wallet.export-transactions-modal.title'))
        ->call('openModal')
        ->assertSee(trans('pages.wallet.export-transactions-modal.title'));
});

it('should close modal', function () {
    Livewire::test(ExportTransactions::class)
        ->assertSee(trans('actions.export'))
        ->call('openModal')
        ->assertSee(trans('pages.wallet.export-transactions-modal.title'))
        ->call('closeModal')
        ->assertDontSee(trans('pages.wallet.export-transactions-modal.title'));
});
