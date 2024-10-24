<?php

declare(strict_types=1);

use App\Http\Livewire\Modals\SubmitExchange;
use App\Mail\ExchangeFormSubmitted;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

it('should render', function () {
    Livewire::test(SubmitExchange::class)
        ->assertSee(trans('actions.submit_exchange'))
        ->assertDontSee(trans('pages.exchanges.submit-modal.title'));
});

it('should open modal', function () {
    Livewire::test(SubmitExchange::class)
        ->assertSee(trans('actions.submit_exchange'))
        ->assertDontSee(trans('pages.exchanges.submit-modal.title'))
        ->call('openModal')
        ->assertSee(trans('pages.exchanges.submit-modal.title'));
});

it('should not send mail if not valid data', function () {
    Mail::fake();

    Livewire::test(SubmitExchange::class)
        ->call('openModal')
        ->assertSet('name', null)
        ->assertSet('website', null)
        ->assertSet('pairs', null)
        ->assertSet('message', null)
        ->call('submit')
        ->assertHasErrors([
            'name'    => 'required',
            'website' => 'required',
            'pairs'   => 'required',
        ]);

    Mail::assertNothingQueued();
});

it('should submit', function () {
    Mail::fake();

    Livewire::test(SubmitExchange::class)
        ->call('openModal')
        ->set('name', 'Potato Exchange')
        ->set('website', 'https://potato.exchange')
        ->set('pairs', 'BTC, USD')
        ->set('message', 'Exciting new universal exchange')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('name', null)
        ->assertSet('website', null)
        ->assertSet('pairs', null)
        ->assertSet('message', null);

    Mail::assertQueued(ExchangeFormSubmitted::class);
});

it('should cancel submission', function () {
    Livewire::test(SubmitExchange::class)
        ->call('openModal')
        ->set('name', 'Potato Exchange')
        ->set('website', 'https://potato.exchange')
        ->set('pairs', 'BTC, USD')
        ->set('message', 'Exciting new universal exchange')
        ->assertSee(trans('pages.exchanges.submit-modal.title'))
        ->call('cancel')
        ->assertSet('name', null)
        ->assertSet('website', null)
        ->assertSet('pairs', null)
        ->assertSet('message', null)
        ->assertDontSee(trans('pages.exchanges.submit-modal.title'));
});

it('should throttle submissions', function () {
    Mail::fake();

    $this->freezeTime();

    Livewire::test(SubmitExchange::class)
        ->call('openModal')
        ->set('name', 'Potato Exchange')
        ->set('website', 'https://potato.exchange')
        ->set('pairs', 'BTC, USD')
        ->set('message', 'Exciting new universal exchange')
        ->call('submit')
        ->assertNotDispatched('toastMessage', [
            'message' => trans('pages.exchanges.submit-modal.throttle_error', ['time' => '1 hour']),
            'type'    => 'warning',
        ])
        ->set('name', 'Potato Exchange')
        ->set('website', 'https://potato.exchange')
        ->set('pairs', 'BTC, USD')
        ->set('message', 'Exciting new universal exchange')
        ->call('submit')
        ->assertNotDispatched('toastMessage', [
            'message' => trans('pages.exchanges.submit-modal.throttle_error', ['time' => '1 hour']),
            'type'    => 'warning',
        ])
        ->set('name', 'Potato Exchange')
        ->set('website', 'https://potato.exchange')
        ->set('pairs', 'BTC, USD')
        ->set('message', 'Exciting new universal exchange')
        ->call('submit')
        ->assertNotDispatched('toastMessage', [
            'message' => trans('pages.exchanges.submit-modal.throttle_error', ['time' => '1 hour']),
            'type'    => 'warning',
        ])
        ->set('name', 'Potato Exchange')
        ->set('website', 'https://potato.exchange')
        ->set('pairs', 'BTC, USD')
        ->set('message', 'Exciting new universal exchange')
        ->call('submit')
        ->assertDispatched('toastMessage', [
            'message' => trans('pages.exchanges.submit-modal.throttle_error', ['time' => '1 hour']),
            'type'    => 'warning',
    ]);

    Mail::assertQueued(ExchangeFormSubmitted::class, 3);
});
