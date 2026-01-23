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
                        ->where('exchanges.noResultsMessage', trans('tables.exchanges.no_results'));
                });
        });
});

it('should sort by name', function ($direction, $order) {
    $this->withoutExceptionHandling();

    Exchange::factory()->create(['id' => 1, 'name' => 'Z Exchange', 'volume' => 100]);
    Exchange::factory()->create(['id' => 2, 'name' => 'A Exchange', 'volume' => 200]);
    Exchange::factory()->create(['id' => 3, 'name' => 'M Exchange', 'volume' => 150]);

    $this->get(route('exchanges', ['sort' => 'name', 'sort-direction' => $direction]))
        ->assertOk()
        ->assertInertia(function (Assert $page) use ($order) {
            $page->component('Resources/Exchanges')
                ->missing('exchanges')
                ->reloadOnly('exchanges', function (Assert $reload) use ($order) {
                    $reload->where('exchanges.data.0.id', $order[0])
                        ->where('exchanges.data.1.id', $order[1])
                        ->where('exchanges.data.2.id', $order[2]);
                });
        });
})->with([
    'ascending'  => ['asc', [2, 3, 1]],
    'descending' => ['desc', [1, 3, 2]],
]);

it('should sort by pairs', function ($direction, $order) {
    $this->withoutExceptionHandling();

    Exchange::factory()->create(['id' => 1, 'btc' => true, 'eth' => false, 'stablecoins' => false, 'other' => false, 'volume' => 100]);
    Exchange::factory()->create(['id' => 2, 'btc' => false, 'eth' => true, 'stablecoins' => true, 'other' => false, 'volume' => 200]);
    Exchange::factory()->create(['id' => 3, 'btc' => false, 'eth' => false, 'stablecoins' => true, 'other' => false, 'volume' => 150]);

    $this->get(route('exchanges', ['sort' => 'top_pairs', 'sort-direction' => $direction]))
        ->assertOk()
        ->assertInertia(function (Assert $page) use ($order) {
            $page->component('Resources/Exchanges')
                ->missing('exchanges')
                ->reloadOnly('exchanges', function (Assert $reload) use ($order) {
                    $reload->where('exchanges.data.0.id', $order[0])
                        ->where('exchanges.data.1.id', $order[1])
                        ->where('exchanges.data.2.id', $order[2]);
                });
        });
})->with([
    'ascending'  => ['asc', [1, 2, 3]],
    'descending' => ['desc', [3, 2, 1]],
]);

it('should sort by volume for matching pairs', function ($direction, $order) {
    $this->withoutExceptionHandling();

    Exchange::factory()->create(['id' => 1, 'btc' => true, 'eth' => false, 'stablecoins' => false, 'other' => false, 'volume' => 100]);
    Exchange::factory()->create(['id' => 2, 'btc' => false, 'eth' => true, 'stablecoins' => true, 'other' => false, 'volume' => 200]);
    Exchange::factory()->create(['id' => 3, 'btc' => false, 'eth' => true, 'stablecoins' => true, 'other' => false, 'volume' => 150]);
    Exchange::factory()->create(['id' => 4, 'btc' => false, 'eth' => false, 'stablecoins' => true, 'other' => false, 'volume' => 300]);

    $this->get(route('exchanges', ['sort' => 'top_pairs', 'sort-direction' => $direction]))
        ->assertOk()
        ->assertInertia(function (Assert $page) use ($order) {
            $page->component('Resources/Exchanges')
                ->missing('exchanges')
                ->reloadOnly('exchanges', function (Assert $reload) use ($order) {
                    $reload->where('exchanges.data.0.id', $order[0])
                        ->where('exchanges.data.1.id', $order[1])
                        ->where('exchanges.data.2.id', $order[2])
                        ->where('exchanges.data.3.id', $order[3]);
                });
        });
})->with([
    'ascending'  => ['asc', [1, 3, 2, 4]],
    'descending' => ['desc', [4, 2, 3, 1]],
]);

it('should sort by price', function ($direction, $order) {
    $this->withoutExceptionHandling();

    Exchange::factory()->create(['id' => 1, 'price' => 1.15, 'volume' => 100]);
    Exchange::factory()->create(['id' => 2, 'price' => 2.15, 'volume' => 200]);
    Exchange::factory()->create(['id' => 3, 'price' => 3.15, 'volume' => 150]);

    $this->get(route('exchanges', ['sort' => 'price', 'sort-direction' => $direction]))
        ->assertOk()
        ->assertInertia(function (Assert $page) use ($order) {
            $page->component('Resources/Exchanges')
                ->missing('exchanges')
                ->reloadOnly('exchanges', function (Assert $reload) use ($order) {
                    $reload->where('exchanges.data.0.id', $order[0])
                        ->where('exchanges.data.1.id', $order[1])
                        ->where('exchanges.data.2.id', $order[2]);
                });
        });
})->with([
    'ascending'  => ['asc', [1, 2, 3]],
    'descending' => ['desc', [3, 2, 1]],
]);

it('should sort by price with null values always last', function ($direction, $order) {
    $this->withoutExceptionHandling();

    Exchange::factory()->create(['id' => 1, 'price' => 1.15, 'volume' => 100]);
    Exchange::factory()->create(['id' => 2, 'price' => null, 'volume' => 200]);
    Exchange::factory()->create(['id' => 3, 'price' => null, 'volume' => 150]);
    Exchange::factory()->create(['id' => 4, 'price' => 3.15, 'volume' => 300]);

    $this->get(route('exchanges', ['sort' => 'price', 'sort-direction' => $direction]))
        ->assertOk()
        ->assertInertia(function (Assert $page) use ($order) {
            $page->component('Resources/Exchanges')
                ->missing('exchanges')
                ->reloadOnly('exchanges', function (Assert $reload) use ($order) {
                    $reload->where('exchanges.data.0.id', $order[0])
                        ->where('exchanges.data.1.id', $order[1])
                        ->where('exchanges.data.2.id', $order[2])
                        ->where('exchanges.data.3.id', $order[3]);
                });
        });
})->with([
    'ascending'  => ['asc', [1, 4, 3, 2]],
    'descending' => ['desc', [4, 1, 3, 2]],
]);

it('should sort by volume', function ($direction, $order) {
    $this->withoutExceptionHandling();

    Exchange::factory()->create(['id' => 1, 'volume' => 100]);
    Exchange::factory()->create(['id' => 2, 'volume' => 200]);
    Exchange::factory()->create(['id' => 3, 'volume' => 150]);

    $this->get(route('exchanges', ['sort' => 'volume', 'sort-direction' => $direction]))
        ->assertOk()
        ->assertInertia(function (Assert $page) use ($order) {
            $page->component('Resources/Exchanges')
                ->missing('exchanges')
                ->reloadOnly('exchanges', function (Assert $reload) use ($order) {
                    $reload->where('exchanges.data.0.id', $order[0])
                        ->where('exchanges.data.1.id', $order[1])
                        ->where('exchanges.data.2.id', $order[2]);
                });
        });
})->with([
    'ascending'  => ['asc', [1, 3, 2]],
    'descending' => ['desc', [2, 3, 1]],
]);

it('should sort by volume for matching sort values', function ($direction, $order) {
    $this->withoutExceptionHandling();

    Exchange::factory()->create(['id' => 1, 'price' => 1.15, 'volume' => 100]);
    Exchange::factory()->create(['id' => 2, 'price' => 1.15, 'volume' => 200]);
    Exchange::factory()->create(['id' => 3, 'price' => 1.15, 'volume' => 150]);

    $this->get(route('exchanges', ['sort' => 'price', 'sort-direction' => $direction]))
        ->assertOk()
        ->assertInertia(function (Assert $page) use ($order) {
            $page->component('Resources/Exchanges')
                ->missing('exchanges')
                ->reloadOnly('exchanges', function (Assert $reload) use ($order) {
                    $reload->where('exchanges.data.0.id', $order[0])
                        ->where('exchanges.data.1.id', $order[1])
                        ->where('exchanges.data.2.id', $order[2]);
                });
        });
})->with([
    'ascending'  => ['asc', [1, 3, 2]],
    'descending' => ['desc', [2, 3, 1]],
]);

it('should sort by volume for invalid sort key', function ($direction, $order) {
    $this->withoutExceptionHandling();

    Exchange::factory()->create(['id' => 1, 'volume' => 100]);
    Exchange::factory()->create(['id' => 2, 'volume' => 200]);
    Exchange::factory()->create(['id' => 3, 'volume' => 150]);

    $this->get(route('exchanges', ['sort' => 'invalid', 'sort-direction' => $direction]))
        ->assertOk()
        ->assertInertia(function (Assert $page) use ($order) {
            $page->component('Resources/Exchanges')
                ->missing('exchanges')
                ->reloadOnly('exchanges', function (Assert $reload) use ($order) {
                    $reload->where('exchanges.data.0.id', $order[0])
                        ->where('exchanges.data.1.id', $order[1])
                        ->where('exchanges.data.2.id', $order[2]);
                });
        });
})->with([
    'ascending'  => ['asc', [2, 3, 1]],
    'descending' => ['desc', [2, 3, 1]],
]);

it('should be possible to successfully send the form', function () {
    Mail::fake();

    $this->post(route('exchanges.submit'), [
        'name'    => 'test',
        'website' => 'http://www.ardenthq.com',
        'pairs'   => 'BTC, ETH, etc',
        'subject' => 'general',
        'message' => 'test',
    ])->assertRedirect(route('exchanges'));
});

it('should show validation error if validation fails', function () {
    $this->post(route('exchanges.submit'), [
        'name'    => 'test',
        'website' => 'test',
        'pairs'   => '',
        'subject' => 'general',
        'message' => 'test',
    ])->assertSessionHasErrors(['website', 'pairs']);
});
