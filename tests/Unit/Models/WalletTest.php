<?php

declare(strict_types=1);

use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Meilisearch\Client as MeilisearchClient;
use Meilisearch\Endpoints\Indexes;

beforeEach(function () {
    $this->subject = Wallet::factory()->create([
        'updated_at' => Carbon::createFromTimestamp(123456789),
        'balance'    => '100000000000',
        'attributes' => [
            'username'             => 'test',
            'validatorVoteBalance' => '200000000000',
        ],
    ]);
});

it('should have many sent transactions', function () {
    expect($this->subject->sentTransactions())->toBeInstanceOf(HasMany::class);
    expect($this->subject->sentTransactions)->toBeInstanceOf(Collection::class);
});

it('should have many received transactions', function () {
    expect($this->subject->receivedTransactions())->toBeInstanceOf(HasMany::class);
    expect($this->subject->receivedTransactions)->toBeInstanceOf(Collection::class);
});

it('should have many blocks', function () {
    expect($this->subject->blocks())->toBeInstanceOf(HasMany::class);
    expect($this->subject->blocks)->toBeInstanceOf(Collection::class);
});

it('has custom scout key', function () {
    expect($this->subject->getScoutKey())->toBe($this->subject->address);
});

it('has custom scout key name', function () {
    expect($this->subject->getScoutKeyName())->toBe('address');
});

it('adds the timestamp from the updated_at column and username when making searchable', function () {
    $mock    = $this->mock(MeilisearchClient::class);
    $indexes = $this->mock(Indexes::class);

    $mock->shouldReceive('index')
        ->withArgs(['wallets'])
        ->andReturn($indexes);

    $indexes->shouldReceive('addDocuments')
        ->withArgs(function ($documents) {
            $document = collect($documents)->first(fn ($document) => $document['address'] === $this->subject->address);

            return $document['username'] === 'test'
                && $document['address'] === $this->subject->address
                && $document['timestamp'] === 123456789;
        });

    // Default value, overriden in phpunit.xml for the tests
    Config::set('scout.driver', 'meilisearch');

    Wallet::makeAllSearchable();

    // Expect no exception to be thrown
    expect(true)->toBeTrue();
});
