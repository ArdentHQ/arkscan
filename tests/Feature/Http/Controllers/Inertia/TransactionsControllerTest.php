<?php

declare(strict_types=1);

use App\Http\Controllers\Inertia\TransactionsController;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    Cache::tags('statistics')->flush();
});

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    $this
        ->get(route('transactions'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Transactions/Transactions')
            ->has('statistics.transactionCount')
            ->has('statistics.volume')
            ->has('statistics.totalFees')
            ->has('statistics.averageFee'));
});

it('should get the transaction stats for the last 24 hours', function () {
    $this->travelTo('2021-04-14 16:02:04');

    Transaction::factory(148)->create([
        'timestamp' => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'value'     => 123 * 1e18,
        'gas_price' => 5000000000,
    ]);

    Transaction::factory(12)->create([
        'timestamp'  => Carbon::parse('2021-04-13 13:02:04')->getTimestampMs(),
        'value'      => 123 * 1e18,
        'gas_price'  => 5000000000,
    ]);

    $this
        ->get(route('transactions'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Transactions/Transactions')
            ->where('statistics.transactionCount', 148)
            ->where('statistics.volume', fn ($value) => abs($value - 18204) < 0.00000001)
            ->where('statistics.totalFees', fn ($value) => abs($value - 0.01554) < 0.00000001)
            ->where('statistics.averageFee', fn ($value) => abs($value - 0.000105) < 0.00000001));

    $this->travelTo('2021-04-15 16:02:04');

    $this
        ->get(route('transactions'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Transactions/Transactions')
            ->where('statistics.transactionCount', 0)
            ->where('statistics.volume', 0)
            ->where('statistics.totalFees', 0)
            ->where('statistics.averageFee', 0));
});

it('should show the correct decimal places for the stats', function ($decimalPlaces, $amount, $fee, $expectedFormattedFee) {
    $this->travelTo('2021-04-14 16:02:04');

    $gasUsed = 21000;

    Transaction::factory()
        ->create([
            'timestamp' => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
            'value'     => BigNumber::new($amount * 1e18),
            'gas_price' => $fee,
            'gas_used'  => $gasUsed,
        ]);

    $formattedFee = $fee * $gasUsed;

    expect((string) $formattedFee)->toEqual($expectedFormattedFee);

    $fee = BigNumber::new($formattedFee)->toFloat();

    $this
        ->get(route('transactions'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Transactions/Transactions')
            ->where('statistics.transactionCount', 1)
            ->where('statistics.volume', fn ($value) => abs($value - $amount) < 0.00000001)
            ->where('statistics.totalFees', fn ($value) => abs($value - $fee) < 0.00000001)
            ->where('statistics.averageFee', fn ($value) => abs($value - $fee) < 0.00000001));
})->with([
    8 => [8, 919123.48392049, 99184739, '2082879519000'],
    7 => [7, 919123.4839204, 99184730, '2082879330000'],
    6 => [6, 919123.483929, 99183900, '2082861900000'],
    5 => [5, 919123.48392, 99739000, '2094519000000'],
    4 => [4, 919123.4839, 99180000, '2082780000000'],
    3 => [3, 919123.489, 47900000, '1005900000000'],
    2 => [2, 919123.48, 99000000, '2079000000000'],
]);

it('should cache the transaction stats for 5 minutes', function () {
    $this->travelTo('2021-04-14 16:02:04');

    Transaction::factory(146)->create([
        'timestamp'       => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'value'           => 123 * 1e18,
        'gas_price'       => 5000000000,
    ]);

    $volume = (123 * 146);

    $this
        ->get(route('transactions'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Transactions/Transactions')
            ->where('statistics.transactionCount', 146)
            ->where('statistics.volume', fn ($value) => abs($value - $volume) < 0.00000001)
            ->where('statistics.totalFees', fn ($value) => abs($value - 0.01533) < 0.00000001)
            ->where('statistics.averageFee', fn ($value) => abs($value - 0.000105) < 0.00000001));

    Transaction::factory(12)->create([
        'timestamp'       => Carbon::parse('2021-04-14 13:03:04')->getTimestampMs(),
        'value'           => 123 * 1e18,
        'gas_price'       => 5000000000,
    ]);

    $this
        ->get(route('transactions'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Transactions/Transactions')
            ->where('statistics.transactionCount', 146)
            ->where('statistics.volume', fn ($value) => abs($value - $volume) < 0.00000001)
            ->where('statistics.totalFees', fn ($value) => abs($value - 0.01533) < 0.00000001)
            ->where('statistics.averageFee', fn ($value) => abs($value - 0.000105) < 0.00000001));

    $this->travelTo('2021-04-14 16:09:04');

    $volume += 123 * 12;

    $this
        ->get(route('transactions'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Transactions/Transactions')
            ->where('statistics.transactionCount', 158)
            ->where('statistics.volume', fn ($value) => abs($value - $volume) < 0.00000001)
            ->where('statistics.totalFees', fn ($value) => abs($value - 0.01659) < 0.00000001)
            ->where('statistics.averageFee', fn ($value) => abs($value - 0.000105) < 0.00000001));
});

it('should return empty transactions and the no-filters message when all filters are disabled', function () {
    $query = collect(TransactionsController::FILTERS)
        ->keys()
        ->mapWithKeys(fn (string $key) => [$key => false])
        ->toArray();

    app()->instance('request', Request::create(route('transactions'), 'GET', $query));

    $controller = new TransactionsController();

    $paginator = $controller->getTransactions();

    expect($paginator->total())->toBe(0);
    expect($controller->getNoResultsMessageProperty($paginator->count()))
        ->toBe(trans('tables.transactions.no_results.no_filters'));
});

it('should return the no-results message when filters are enabled but there are no results', function () {
    app()->instance('request', Request::create(route('transactions'), 'GET', [
        'transfers'           => true,
        'multipayments'       => false,
        'votes'               => false,
        'validator'           => false,
        'username'            => false,
        'contract_deployment' => false,
        'others'              => false,
    ]));

    $controller = new TransactionsController();

    $paginator = $controller->getTransactions();

    expect($paginator->total())->toBe(0);
    expect($controller->getNoResultsMessageProperty($paginator->count()))
        ->toBe((string) trans('tables.transactions.no_results.no_results'));
});

it('should return transactions and no message when results exist', function () {
    $transaction = Transaction::factory()->transfer()->create();
    Wallet::factory()->create(['address' => $transaction->from]);
    Wallet::factory()->create(['address' => $transaction->to]);

    app()->instance('request', Request::create(route('transactions'), 'GET', [
        'transfers'           => true,
        'multipayments'       => false,
        'votes'               => false,
        'validator'           => false,
        'username'            => false,
        'contract_deployment' => false,
        'others'              => false,
        'per-page'            => 10,
        'page'                => 1,
    ]));

    $controller = new TransactionsController();

    $paginator = $controller->getTransactions();

    expect($paginator->total())->toBe(1);
    expect($paginator->items()[0])->toBeInstanceOf(\App\DTO\Inertia\Transaction::class);
    expect($controller->getNoResultsMessageProperty($paginator->count()))->toBeNull();
});
