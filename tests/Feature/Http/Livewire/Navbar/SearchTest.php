<?php

declare(strict_types=1);

use App\Http\Livewire\Navbar\Search;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
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

it('should redirect on submit', function () {
    $block = Block::factory()->create();

    Livewire::test(Search::class)
        ->set('query', $block->id)
        ->assertSee($block->id)
        ->call('goToFirstResult')
        ->assertRedirect(route('block', $block));
});

it('should do nothing if no results on submit', function () {
    $block = Block::factory()->create();

    Livewire::test(Search::class)
        ->set('query', 'non-existant block id')
        ->assertDontSee($block->id)
        ->call('goToFirstResult')
        ->assertOk();
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

it('should search for known wallets addresses with meilisearch', function () {
    // Default value, overriden in phpunit.xml for the tests
    Config::set('scout.driver', 'meilisearch');

    // Mock the Meilisearch client and indexes
    $mock    = $this->mock(MeilisearchClient::class);
    $indexes = $this->mock(Indexes::class);
    $mock->shouldReceive('index')->andReturn($indexes);
    $indexes->shouldReceive('addDocuments');

    $knownWalletsUrl = 'https://knownwallets.com/known-wallets.json';

    Config::set('explorer.networks.development.knownWallets', $knownWalletsUrl);

    Http::fake(Http::response([
        [
            'type'    => 'team',
            'name'    => 'Alfys hot Wallet',
            'address' => 'AagJoLEnpXYkxYdYkmdDSNMLjjBkLJ6T67',
        ],
        [
            'type'    => 'team',
            'name'    => 'other wallet',
            'address' => 'Ac6ofoku9qMurd3uibDbEqg6EFrENLXq2d',
        ],
        [
            'type'    => 'team',
            'name'    => 'the alf wallet',
            'address' => 'AaH5Fx78kge1mPSPZEysW5nwubR6QCFQtk',
        ],
    ], 200));

    $knownWallet = Wallet::factory()->create([
        'address' => 'AagJoLEnpXYkxYdYkmdDSNMLjjBkLJ6T67',
    ]);
    $knownWallet2 = Wallet::factory()->create([
        'address' => 'AaH5Fx78kge1mPSPZEysW5nwubR6QCFQtk',
    ]);

    $this->mock(MeilisearchEngine::class)
        ->shouldReceive('multiSearch')
        ->withArgs(function ($params) {
            // 3 for all index types + 2 for known wallets
            return count($params) === 5 &&
                collect($params)->every(fn ($param) => $param instanceof SearchQuery);
        })
        ->once()
        ->andReturn([
            'results' => [
                [
                    'indexUid' => 'wallets',
                    'hits'     => [
                        $knownWallet->toSearchableArray(),
                        $knownWallet2->toSearchableArray(),
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
        ->set('query', 'alf')
        ->assertSee('AagJoLEnpXYkxYdYkmdDSNMLjjBkLJ6T67')
        ->assertSee('AaH5Fx78kge1mPSPZEysW5nwubR6QCFQtk')
        ->assertDontSee('Ac6ofoku9qMurd3uibDbEqg6EFrENLXq2d');
});
