<?php

declare(strict_types=1);

use App\Models\Exchange;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    $this->get(route('exchanges'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Resources/Exchanges')
            ->where('typeOptions', [
                [
                    'title' => trans('general.all'),
                    'value' => 'all',
                ],
                [
                    'title' => trans('pages.exchanges.type.exchanges'),
                    'value' => 'exchanges',
                ],
                [
                    'title' => trans('pages.exchanges.type.aggregators'),
                    'value' => 'aggregators',
                ],
            ])
            ->where('pairOptions', [
                [
                    'title' => trans('general.all'),
                    'value' => 'all',
                ],
                [
                    'title' => trans('pages.exchanges.pair.btc'),
                    'value' => 'btc',
                ],
                [
                    'title' => trans('pages.exchanges.pair.eth'),
                    'value' => 'eth',
                ],
                [
                    'title' => trans('pages.exchanges.pair.stablecoins'),
                    'value' => 'stablecoins',
                ],
                [
                    'title' => trans('pages.exchanges.pair.other'),
                    'value' => 'other',
                ],
            ])
            ->missing('exchanges'));
});

it('should load exchanges', function () {
    $this->withoutExceptionHandling();

    Exchange::factory(15)->create();

    $this->get(route('exchanges'))
        ->assertOk()
        ->assertInertia(function (Assert $page) {
            $page->component('Resources/Exchanges')
                ->missing('exchanges')
                ->reloadOnly('exchanges', function (Assert $reload) {
                    $reload->has('exchanges.data', 15)
                        ->where('exchanges.current_page', 1)
                        ->where('exchanges.per_page', 15);
                });
        });
});

it('should handle no exchanges', function () {
    $this->withoutExceptionHandling();

    $this->get(route('exchanges'))
        ->assertOk()
        ->assertInertia(function (Assert $page) {
            $page->component('Resources/Exchanges')
                ->missing('exchanges')
                ->reloadOnly('exchanges', function (Assert $reload) {
                    $reload->has('exchanges.data', 0)
                        ->where('exchanges.noResultsMessage', trans('tables.exchanges.no_results.no_results'));
                });
        });
});

it('should be possible to successfully send the form', function () {
    Mail::fake();

    $this->post(route('exchanges.submit'), [
        'name'    => 'test',
        'website' => 'http://www.ardenthq.com',
        'pairs' => 'BTC, ETH, etc',
        'subject' => 'general',
        'message' => 'test',
    ])->assertRedirect(route('exchanges'));
});

it('should show validation error if validation fails', function () {
    $this->post(route('exchanges.submit'), [
        'name'    => 'test',
        'website' => 'test',
        'pairs' => '',
        'subject' => 'general',
        'message' => 'test',
    ])->assertSessionHasErrors(['website', 'pairs']);
});
