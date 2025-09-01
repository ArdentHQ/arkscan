<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\MultiPayment;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Config;
use Meilisearch\Client as MeilisearchClient;
use Meilisearch\Endpoints\Indexes;

beforeEach(function () {
    $this->recipient = Wallet::factory()->create();
    $this->subject   = Transaction::factory()->create([
        'gas_price'         => 1,
        'value'             => 2 * 1e18,
        'to'                => $this->recipient,
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
            'to' => null,
        ]);

    $this->expectException(Exception::class);

    $transaction->recipient();
});

it('should throw an exception if a vote has no recipient', function () {
    $transaction = Transaction::factory()
        ->vote('') // Invalid address
        ->create([
            'to' => null,
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

it('should calculate fee', function () {
    $transaction = Transaction::factory()->create([
        'gas_price' => 54,
        'gas_used'  => 21000,
    ]);

    expect($transaction->fresh()->fee()->toNumber())->toBe(1134000);
});

it('should return error', function () {
    $transaction = Transaction::factory()->create([
        'status'           => false,
        'decoded_error' => 'CallerIsNotValidator',
    ]);

    expect($transaction->transactionError())->toBe('Caller Is Not Validator');
});

it('should return error for insufficient gas', function () {
    $transaction = Transaction::factory()->create([
        'gas'           => BigNumber::new(80131),
        'gas_used'      => BigNumber::new(79326),
        'status'        => false,
        'decoded_error' => 'execution reverted',
    ]);

    expect($transaction->transactionError())->toBe('Out of gas?');
});

it('should not return error for insufficient gas if receipt did not fail', function () {
    $transaction = Transaction::factory()->create([
        'gas'      => BigNumber::new(80131),
        'gas_used' => BigNumber::new(79326),
        'status'   => true,
    ]);

    expect($transaction->transactionError())->toBeNull();
});

it('should not modify gas used instance when getting receipt error', function () {
    $gasUsed     = BigNumber::new(79326);
    $transaction = Transaction::factory()->create([
        'gas'           => BigNumber::new(80131),
        'gas_used'      => $gasUsed,
        'status'        => false,
        'decoded_error' => 'execution reverted',
    ]);

    expect($transaction->transactionError())->toBe('Out of gas?');
    expect($transaction->gas_used)->toEqual($gasUsed);
});

it('should not format errors with a space', function () {
    $transaction = Transaction::factory()->create([
        'status'           => false,
        'decoded_error' => 'Error (Must send exactly 0.001 ETH to set message)',
    ]);

    expect($transaction->transactionError())->toBe('Error (Must send exactly 0.001 ETH to set message)');
});

it('should return null if no receipt error', function () {
    $transaction = Transaction::factory()->create([
        'status' => false,
    ]);

    expect($transaction->transactionError())->toBeNull();
});

it('should return null if no receipt record', function () {
    $transaction = Transaction::factory()->create();

    expect($transaction->transactionError())->toBeNull();
});

it('should return null if no valid error', function () {
    $transaction = Transaction::factory()->create([
        'status' => false,
    ]);

    expect($transaction->transactionError())->toBeNull();
});

it('should get recipients', function () {
    $recipients = [
        Wallet::factory()->create()->address,
        Wallet::factory()->create()->address,
    ];

    $transaction = Transaction::factory()
        ->multiPayment($recipients, [
            BigNumber::new(1),
            BigNumber::new(1),
        ])
        ->create();

    MultiPayment::factory()
        ->count(2)
        ->state(new Sequence(
            ['to' => $recipients[0]],
            ['to' => $recipients[1]],
        ))
        ->create([
            'from'   => $transaction->from,
            'hash'   => $transaction->hash,
            'amount' => BigNumber::new(1),
        ]);

    expect($transaction->multiPaymentRecipients->count())->toBe(2);
    expect($transaction->multiPaymentRecipients->first()->to)->toBe($recipients[0]);
    expect($transaction->multiPaymentRecipients->last()->to)->toBe($recipients[1]);
});
