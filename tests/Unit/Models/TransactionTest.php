<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Config;
use Meilisearch\Client as MeilisearchClient;
use Meilisearch\Endpoints\Indexes;

beforeEach(function () {
    $this->recipient = Wallet::factory()->create();
    $this->subject   = Transaction::factory()->create([
        'fee'          => '100000000',
        'amount'       => '200000000',
        'recipient_id' => $this->recipient,
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

it('should belong to a recipient', function () {
    Wallet::factory()->create(['address' => $this->recipient->address]);

    expect($this->subject->recipient())->toEqual($this->recipient->fresh());
});

it('should get recipient if vote', function () {
    $validator = Wallet::factory()->activeValidator()->create();

    $transaction = Transaction::factory()
        ->vote()
        ->create([
            'recipient_id' => null,
            'asset'        => [
                'votes'   => [$validator->public_key],
                'unvotes' => [],
            ],
        ]);

    expect($transaction->recipient())->toEqual($validator->fresh());
});

it('should get recipient if unvote', function () {
    $validator = Wallet::factory()->activeValidator()->create();

    $transaction = Transaction::factory()
        ->unvote()
        ->create([
            'recipient_id' => null,
            'asset'        => [
                'unvotes' => [$validator->public_key],
                'votes'   => [],
            ],
        ]);

    expect($transaction->recipient())->toEqual($validator->fresh());
});

it('should get vote recipient if vote combination', function () {
    $validator    = Wallet::factory()->activeValidator()->create();
    $oldValidator = Wallet::factory()->activeValidator()->create();

    $transaction = Transaction::factory()
        ->voteCombination()
        ->create([
            'recipient_id' => null,
            'asset'        => [
                'votes'   => [$validator->public_key],
                'unvotes' => [$oldValidator->public_key],
            ],
        ]);

    expect($transaction->recipient())->toEqual($validator->fresh());
});

it('should get vendorfield value multiple times despite resource', function () {
    $transaction = Transaction::factory()->transfer()->create([
        'recipient_id' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
        'fee'          => '10000000', // 0.1
        'amount'       => '200000000', // 2
        'vendor_field' => '0xRKeoIZ9Kh2g4HslgeHr5B9yblHbnwWYgfeFgO36n0',
    ]);

    expect($transaction->vendor_field)->toBe('0xRKeoIZ9Kh2g4HslgeHr5B9yblHbnwWYgfeFgO36n0');
    expect($transaction->vendorField())->toBe('0xRKeoIZ9Kh2g4HslgeHr5B9yblHbnwWYgfeFgO36n0');

    $transaction = Transaction::find($transaction->id);

    expect(is_resource($transaction->vendor_field))->toBeTrue();
    expect($transaction->vendorField())->toBe('0xRKeoIZ9Kh2g4HslgeHr5B9yblHbnwWYgfeFgO36n0');
    expect($transaction->vendorField())->toBe('0xRKeoIZ9Kh2g4HslgeHr5B9yblHbnwWYgfeFgO36n0');
    expect($transaction->vendorField())->toBe('0xRKeoIZ9Kh2g4HslgeHr5B9yblHbnwWYgfeFgO36n0');
    expect($transaction->vendorField())->toBe('0xRKeoIZ9Kh2g4HslgeHr5B9yblHbnwWYgfeFgO36n0');
    expect($transaction->vendorField())->toBe('0xRKeoIZ9Kh2g4HslgeHr5B9yblHbnwWYgfeFgO36n0');
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
            $document = collect($documents)->first(fn ($document) => $document['id'] === $transaction->id);

            return json_encode($document) === json_encode($transaction->toSearchableArray());
        });

    // Default value, overriden in phpunit.xml for the tests
    Config::set('scout.driver', 'meilisearch');

    Transaction::makeAllSearchable();

    // Expect no exception to be thrown
    expect(true)->toBeTrue();
});
