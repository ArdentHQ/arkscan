<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Search\TransactionSearch;

use App\Services\Timestamp;
use Carbon\Carbon;
use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should search for a transaction by id', function () {
    $transaction = Transaction::factory(10)->create()[0];
    $transaction->update(['vendor_field' => 'Hello World']);

    $result = (new TransactionSearch())->search([
        'smartBridge' => $transaction->vendor_field,
    ]);

    expect($result->get())->toHaveCount(1);
});

it('should search for a transaction by vendor field', function () {
    $transaction = Transaction::factory(10)->create()[0];

    $result = (new TransactionSearch())->search([
        'term' => $transaction->id,
    ]);

    expect($result->get())->toHaveCount(1);
});

it('should search for transactions by timestamp minimum', function () {
    $today = Carbon::now();
    $todayGenesis = Timestamp::fromUnix($today->unix())->unix();

    $yesterday = Carbon::now()->subDay();
    $yesterdayGenesis = Timestamp::fromUnix($yesterday->unix())->unix();

    Transaction::factory(10)->create(['timestamp' => $todayGenesis]);
    Transaction::factory(10)->create(['timestamp' => $yesterdayGenesis]);

    $result = (new TransactionSearch())->search([
        'dateFrom' => $today->toString(),
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for transactions by timestamp maximum', function () {
    $today = Carbon::now();
    $todayGenesis = Timestamp::fromUnix($today->unix())->unix();

    $yesterday = Carbon::now()->subDay();
    $yesterdayGenesis = Timestamp::fromUnix($yesterday->unix())->unix();

    Transaction::factory(10)->create(['timestamp' => $todayGenesis]);
    Transaction::factory(10)->create(['timestamp' => $yesterdayGenesis]);

    $result = (new TransactionSearch())->search([
        'dateTo' => $yesterday->toString(),
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for transactions by timestamp range', function () {
    $today = Carbon::now();
    $todayGenesis = Timestamp::fromUnix($today->unix())->unix();

    $yesterday = Carbon::now()->subDay();
    $yesterdayGenesis = Timestamp::fromUnix($yesterday->unix())->unix();

    Transaction::factory(10)->create(['timestamp' => $todayGenesis]);
    Transaction::factory(10)->create(['timestamp' => $yesterdayGenesis]);

    $result = (new TransactionSearch())->search([
        'dateFrom' => $yesterday->toString(),
        'dateTo'   => $yesterday->toString(),
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by amount minimum', function () {
    Transaction::factory(10)->create(['amount' => 1000 * 1e8]);
    Transaction::factory(10)->create(['amount' => 2000 * 1e8]);

    $result = (new TransactionSearch())->search([
        'amountFrom' => 2000,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by amount maximum', function () {
    Transaction::factory(10)->create(['amount' => 1000 * 1e8]);
    Transaction::factory(10)->create(['amount' => 2000 * 1e8]);

    $result = (new TransactionSearch())->search([
        'amountTo' => 1000,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by amount range', function () {
    Transaction::factory(10)->create(['amount' => 1000 * 1e8]);
    Transaction::factory(10)->create(['amount' => 2000 * 1e8]);

    $result = (new TransactionSearch())->search([
        'amountFrom' => 500,
        'amountTo'   => 1500,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by fee minimum', function () {
    Transaction::factory(10)->create(['fee' => 1000 * 1e8]);
    Transaction::factory(10)->create(['fee' => 2000 * 1e8]);

    $result = (new TransactionSearch())->search([
        'feeFrom' => 2000,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by fee maximum', function () {
    Transaction::factory(10)->create(['fee' => 1000 * 1e8]);
    Transaction::factory(10)->create(['fee' => 2000 * 1e8]);

    $result = (new TransactionSearch())->search([
        'feeTo' => 1000,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by fee range', function () {
    Transaction::factory(10)->create(['fee' => 1000 * 1e8]);
    Transaction::factory(10)->create(['fee' => 2000 * 1e8]);

    $result = (new TransactionSearch())->search([
        'feeFrom' => 500,
        'feeTo'   => 1500,
    ]);

    expect($result->get())->toHaveCount(10);
});
