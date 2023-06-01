<?php

declare(strict_types=1);

use App\Http\Livewire\Navbar\Search;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\Config;
use Laravel\Scout\Engines\MeilisearchEngine;
use Livewire\Livewire;
use Meilisearch\Client as MeilisearchClient;
use Meilisearch\Contracts\SearchQuery;
use Meilisearch\Endpoints\Indexes;

it('should search for a wallet', function () {
    $wallet      = Wallet::factory()->create();
    $otherWallet = Wallet::factory()->create();
    Transaction::factory()->create();
    $block = Block::factory()->create();
    Transaction::factory()->create(['block_id' => $block->id]);

    Livewire::test(Search::class)
        ->set('query', $wallet->address)
        ->assertSee($wallet->address)
        ->assertDontSee($otherWallet->address);
});

it('should search with meilisearch', function () {
    // Default value, overriden in phpunit.xml for the tests
    Config::set('scout.driver', 'meilisearch');

    // Mock the Meilisearch client and indexes
    $mock    = $this->mock(MeilisearchClient::class);
    $indexes = $this->mock(Indexes::class);
    $mock->shouldReceive('index')->andReturn($indexes);
    $indexes->shouldReceive('addDocuments');

    $wallet      = Wallet::factory()->create();
    $otherWallet = Wallet::factory()->create();

    $this->mock(MeilisearchEngine::class)
        ->shouldReceive('multiSearch')
        ->withArgs(function ($params) {
            return count($params) === 3 &&
                collect($params)->every(fn ($param) => $param instanceof SearchQuery);
        })
        ->once()
        ->andReturn([
            'results' => [
                [
                    'indexUid' => 'wallets',
                    'hits'     => [
                        $wallet->toSearchableArray(),
                    ],
                ],
                [
                    'indexUid' => 'transactions',
                    'hits'     => [],
                ],
                [
                    'indexUid' => 'blocks',
                    'hits'     => [],
                ],
            ],
        ]);

    Livewire::test(Search::class)
        ->set('query', $wallet->address)
        ->assertSee($wallet->address)
        ->assertDontSee($otherWallet->address);
});

it('should search for a wallet username over a block generator', function () {
    $wallet = Wallet::factory()->create([
        'attributes' => [
            'delegate' => [
                'username' => 'pieface',
            ],
        ],
    ]);
    $block = Block::factory()->create([
        'generator_public_key' => $wallet->public_key,
    ]);
    Transaction::factory()->create(['block_id' => $block->id]);

    Livewire::test(Search::class)
        ->set('query', $wallet->address)
        ->assertSee($wallet->address);
});

it('should search for a transaction', function () {
    Transaction::factory()->create();
    $block = Block::factory()->create();
    Transaction::factory()->create(['block_id' => $block->id]);
    $transaction = Transaction::factory()->create();

    Livewire::test(Search::class)
        ->set('query', $transaction->id)
        ->assertSee($transaction->id);
});

it('should search for a block', function () {
    $block = Block::factory()->create();

    Livewire::test(Search::class)
        ->set('query', $block->id)
        ->assertSee($block->id);
});

it('should clear search query', function () {
    $block = Block::factory()->create();

    Livewire::test(Search::class)
        ->set('query', $block->id)
        ->assertSee($block->id)
        ->call('clear')
        ->assertDontSee($block->id)
        ->assertOk();
});
