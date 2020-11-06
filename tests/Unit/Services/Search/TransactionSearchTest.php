<?php

declare(strict_types=1);

use App\Models\Transaction;

use App\Models\Wallet;
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

it('should search for transactions by amount minimum', function () {
    Transaction::factory(10)->create(['amount' => 1000 * 1e8]);
    Transaction::factory(10)->create(['amount' => 2000 * 1e8]);

    $result = (new TransactionSearch())->search([
        'amountFrom' => 2000,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for transactions by amount maximum', function () {
    Transaction::factory(10)->create(['amount' => 1000 * 1e8]);
    Transaction::factory(10)->create(['amount' => 2000 * 1e8]);

    $result = (new TransactionSearch())->search([
        'amountTo' => 1000,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for transactions by amount range', function () {
    Transaction::factory(10)->create(['amount' => 1000 * 1e8]);
    Transaction::factory(10)->create(['amount' => 2000 * 1e8]);

    $result = (new TransactionSearch())->search([
        'amountFrom' => 500,
        'amountTo'   => 1500,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for transactions by fee minimum', function () {
    Transaction::factory(10)->create(['fee' => 1000 * 1e8]);
    Transaction::factory(10)->create(['fee' => 2000 * 1e8]);

    $result = (new TransactionSearch())->search([
        'feeFrom' => 2000,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for transactions by fee maximum', function () {
    Transaction::factory(10)->create(['fee' => 1000 * 1e8]);
    Transaction::factory(10)->create(['fee' => 2000 * 1e8]);

    $result = (new TransactionSearch())->search([
        'feeTo' => 1000,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for transactions by fee range', function () {
    Transaction::factory(10)->create(['fee' => 1000 * 1e8]);
    Transaction::factory(10)->create(['fee' => 2000 * 1e8]);

    $result = (new TransactionSearch())->search([
        'feeFrom' => 500,
        'feeTo'   => 1500,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for transactions by wallet with an address', function () {
    Transaction::factory(10)->create();

    $wallet = Wallet::factory()->create([
        'address'    => 'someaddress',
        'public_key' => 'somepubkey',
    ]);

    Transaction::factory()->create([
        'sender_public_key' => $wallet->public_key,
    ]);

    Transaction::factory()->create([
        'recipient_id' => $wallet->address,
    ]);

    Transaction::factory()->create([
        'asset' => [
            'payments' => [
                ['amount' => 10, 'recipientId' => $wallet->address],
            ],
        ],
    ]);

    $result = (new TransactionSearch())->search([
        'term' => $wallet->address,
    ]);

    expect($result->get())->toHaveCount(3);
});

it('should search for transactions by wallet with a public key', function () {
    Transaction::factory(10)->create();

    $wallet = Wallet::factory()->create([
        'public_key' => 'somepublickey',
    ]);

    Transaction::factory()->create([
        'sender_public_key' => $wallet->public_key,
    ]);

    Transaction::factory()->create([
        'recipient_id' => $wallet->address,
    ]);

    Transaction::factory()->create([
        'asset' => [
            'payments' => [
                ['amount' => 10, 'recipientId' => $wallet->address],
            ],
        ],
    ]);

    $result = (new TransactionSearch())->search([
        'term' => $wallet->public_key,
    ]);

    expect($result->get())->toHaveCount(3);
});

it('should search for transactions by wallet with a username', function () {
    Transaction::factory(10)->create();

    $wallet = Wallet::factory()->create([
        'public_key' => 'somepubkey',
        'attributes' => [
            'delegate' => [
                'username' => 'johndoe',
            ],
        ],
    ]);

    Transaction::factory()->create([
        'sender_public_key' => $wallet->public_key,
    ]);

    Transaction::factory()->create([
        'recipient_id' => $wallet->address,
    ]);

    Transaction::factory()->create([
        'asset' => [
            'payments' => [
                ['amount' => 10, 'recipientId' => $wallet->address],
            ],
        ],
    ]);

    $result = (new TransactionSearch())->search([
        'term' => 'johndoe',
    ]);

    expect($result->get())->toHaveCount(3);
});

it('should search for transactions by block with an ID', function () {
    Transaction::factory(10)->create();

    Transaction::factory()->create([
        'block_id' => 'blockid',
    ]);

    $result = (new TransactionSearch())->search([
        'term' => 'blockid',
    ]);

    expect($result->get())->toHaveCount(1);
});

it('should search for transactions by block with a height', function () {
    Transaction::factory(10)->create();

    Transaction::factory()->create([
        'block_height' => 123456789,
    ]);

    $result = (new TransactionSearch())->search([
        'term' => 123456789,
    ]);

    expect($result->get())->toHaveCount(1);
});
