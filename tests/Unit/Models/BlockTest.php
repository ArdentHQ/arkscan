<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Meilisearch\Client as MeilisearchClient;
use Meilisearch\Endpoints\Indexes;

beforeEach(function () {
    $previousBlock = Block::factory()->create(['height' => 1]);

    $this->subject = Block::factory()->create([
        'previous_block' => $previousBlock->id,
        'height'         => 10000,
        'total_amount'   => 50 * 1e18,
        'total_fee'      => 48 * 1e18,
        'reward'         => 2 * 1e18,
    ]);
});

it('should have transactions', function () {
    expect($this->subject->transactions())->toBeInstanceOf(HasMany::class);
    expect($this->subject->transactions)->toBeInstanceOf(Collection::class);
});

it('should have a validator that forged the block', function () {
    Wallet::factory()->create(['address' => $this->subject->generator_address]);

    expect($this->subject->validator())->toBeInstanceOf(BelongsTo::class);
    expect($this->subject->validator)->toBeInstanceOf(Wallet::class);
});

it('should have a previous block', function () {
    expect($this->subject->previous())->toBeInstanceOf(HasOne::class);
    expect($this->subject->previous)->toBeInstanceOf(Block::class);
});

it('makes block searchable', function () {
    $block = Block::factory()->create();

    $mock    = $this->mock(MeilisearchClient::class);
    $indexes = $this->mock(Indexes::class);

    $mock->shouldReceive('index')
        ->withArgs(['blocks'])
        ->andReturn($indexes);

    $indexes->shouldReceive('addDocuments')
        ->withArgs(function ($documents) use ($block) {
            $document = collect($documents)->first(fn ($document) => $document['id'] === $block->id);

            return json_encode($document) === json_encode($block->toSearchableArray());
        });

    // Default value, overriden in phpunit.xml for the tests
    Config::set('scout.driver', 'meilisearch');

    Block::makeAllSearchable();

    // Expect no exception to be thrown
    expect(true)->toBeTrue();
});
