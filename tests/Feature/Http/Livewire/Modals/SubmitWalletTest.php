<?php

declare(strict_types=1);

use App\Http\Livewire\Modals\SubmitWallet;
use App\Mail\WalletFormSubmitted;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

it('should render', function () {
    Livewire::test(SubmitWallet::class)
        ->assertSee(trans('actions.submit_wallet'))
        ->assertDontSee(trans('pages.compatible-wallets.submit-modal.title'));
});

it('should open modal', function () {
    Livewire::test(SubmitWallet::class)
        ->assertSee(trans('actions.submit_wallet'))
        ->assertDontSee(trans('pages.compatible-wallets.submit-modal.title'))
        ->call('openModal')
        ->assertSee(trans('pages.compatible-wallets.submit-modal.title'));
});

it('should not send mail if not valid data', function () {
    Mail::fake();

    Livewire::test(SubmitWallet::class)
        ->call('openModal')
        ->assertSet('name', null)
        ->assertSet('website', null)
        ->assertSet('message', null)
        ->call('submit')
        ->assertHasErrors([
            'name'    => 'required',
            'website' => 'required',
            'message' => 'required',
        ]);

    Mail::assertNothingQueued();
});

it('should submit', function () {
    Mail::fake();

    Livewire::test(SubmitWallet::class)
        ->call('openModal')
        ->set('name', 'Potato Wallet')
        ->set('website', 'https://potato.wallet')
        ->set('message', 'Exciting new universal wallet')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('name', null)
        ->assertSet('website', null)
        ->assertSet('message', null);

    Mail::assertQueued(WalletFormSubmitted::class);
});

it('should throttle submissions', function () {
    Mail::fake();

    $this->freezeTime();

    Livewire::test(SubmitWallet::class)
        ->call('openModal')
        ->set('name', 'Potato Wallet')
        ->set('website', 'https://potato.wallet')
        ->set('message', 'Exciting new universal wallet')
        ->call('submit')
        ->assertNotEmitted('toastMessage', [trans('pages.compatible-wallets.submit-modal.throttle_error', ['time' => '1 hour']), 'warning'])
        ->set('name', 'Potato Wallet')
        ->set('website', 'https://potato.wallet')
        ->set('message', 'Exciting new universal wallet')
        ->call('submit')
        ->assertNotEmitted('toastMessage', [trans('pages.compatible-wallets.submit-modal.throttle_error', ['time' => '1 hour']), 'warning'])
        ->set('name', 'Potato Wallet')
        ->set('website', 'https://potato.wallet')
        ->set('message', 'Exciting new universal wallet')
        ->call('submit')
        ->assertNotEmitted('toastMessage', [trans('pages.compatible-wallets.submit-modal.throttle_error', ['time' => '1 hour']), 'warning'])
        ->set('name', 'Potato Wallet')
        ->set('website', 'https://potato.wallet')
        ->set('message', 'Exciting new universal wallet')
        ->call('submit')
        ->assertEmitted('toastMessage', [trans('pages.compatible-wallets.submit-modal.throttle_error', ['time' => '1 hour']), 'warning']);

    Mail::assertQueued(WalletFormSubmitted::class, 3);
});
