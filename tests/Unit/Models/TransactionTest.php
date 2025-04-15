<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Receipt;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Config;
use Meilisearch\Client as MeilisearchClient;
use Meilisearch\Endpoints\Indexes;

beforeEach(function () {
    $this->recipient = Wallet::factory()->create();
    $this->subject   = Transaction::factory()->create([
        'gas_price'         => 1,
        'amount'            => 2 * 1e18,
        'recipient_address' => $this->recipient,
    ]);
});

it('should belong to a block', function () {
    expect($this->subject->block())->toBeInstanceOf(BelongsTo::class);
    expect($this->subject->block)->toBeInstanceOf(Block::class);
});

it('should belong to a sender', function () {
    Wallet::factory()->create(['public_key' => $this->subject->sender_public_key]);

    expect($this->subject->sender())->toBeInstanceOf(BelongsTo::class);
    expect($this->subject->sender)->toBeInstanceOf(Wallet::class);
});

it('should throw an exception if the transaction has no recipient', function () {
    $transaction = Transaction::factory()
        ->create([
            'recipient_address' => null,
        ]);

    $this->expectException(Exception::class);

    $transaction->recipient();
});

it('should throw an exception if a vote has no recipient', function () {
    $transaction = Transaction::factory()
        ->vote('') // Invalid address
        ->create([
            'recipient_address' => null,
        ]);

    $this->expectException(Exception::class);

    $transaction->recipient();
});

it('makes transactions searchable', function () {
    $transaction = Transaction::factory()->create();

    $mock    = $this->mock(MeilisearchClient::class);
    $indexes = $this->mock(Indexes::class);

    $mock->shouldReceive('index')
        ->withArgs(['transactions'])
        ->andReturn($indexes);

    $indexes->shouldReceive('addDocuments')
        ->withArgs(function ($documents) use ($transaction) {
            $document = collect($documents)->first(fn ($document) => $document['hash'] === $transaction->hash);

            return json_encode($document) === json_encode($transaction->toSearchableArray());
        });

    // Default value, overriden in phpunit.xml for the tests
    Config::set('scout.driver', 'meilisearch');

    Transaction::makeAllSearchable();

    // Expect no exception to be thrown
    expect(true)->toBeTrue();
});

it('should calculate fee with receipt', function () {
    $transaction = Transaction::factory()->create([
        'gas_price' => 54,
    ]);

    Receipt::factory()->create([
        'transaction_hash' => $transaction->hash,
        'gas_used' => 21000,
    ]);

    expect($transaction->fresh()->fee()->toNumber())->toBe(1134000);
});

it('should return gas price if no receipt', function () {
    $transaction = Transaction::factory()->create([
        'gas_price' => 54,
    ]);

    expect($transaction->fresh()->fee()->toNumber())->toBe(54);
});
