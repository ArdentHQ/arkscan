<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Token;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use App\Services\Identity;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Laravel\Scout\Engines\MeilisearchEngine;
use Meilisearch\Client as MeilisearchClient;
use Meilisearch\Contracts\SearchQuery;
use Meilisearch\Endpoints\Indexes;

it('returns an empty result set when the query is missing', function () {
    $this
        ->getJson(route('navbar-search.index'))
        ->assertOk()
        ->assertJson([
            'results'    => [],
            'hasResults' => false,
        ]);
});

it('returns wallet results matching the query', function () {
    $wallet = Wallet::factory()->create();
    Wallet::factory()->create();

    $response = $this
        ->getJson(route('navbar-search.index', ['query' => $wallet->address]))
        ->assertOk()
        ->assertJson([
            'hasResults' => true,
        ]);

    expect($response->json('results.0.identifier'))->toBe($wallet->address);
    expect($response->json('results.0.type'))->toBe('wallet');
    expect($response->json('results.0.data.address'))->toBe($wallet->address);
});

it('returns block results including validator wallet metadata', function () {
    Cache::tags('wallet')->flush();

    $validator = Wallet::factory()->create([
        'address' => '0x'.str_repeat('1', 40),
    ]);

    $token = Token::factory()->create([
        'address' => $validator->address,
    ]);

    $walletCache = new WalletCache();
    $walletCache->setWalletNameByAddress($validator->address, 'Validator One');
    $walletCache->setTokens(collect([strtolower($validator->address) => $token]));

    $block = Block::factory()->create([
        'hash'               => str_repeat('a', 64),
        'proposer'           => $validator->address,
        'transactions_count' => 12,
    ]);

    $response = $this
        ->getJson(route('navbar-search.index', ['query' => $block->hash]))
        ->assertOk();

    $blockResult = collect($response->json('results'))
        ->first(fn ($result) => $result['type'] === 'block');

    expect($blockResult)->not->toBeNull();
    expect($blockResult['type'])->toBe('block');
    expect($blockResult['identifier'])->toBe($block->hash);
    expect($blockResult['data']['hash'])->toBe($block->hash);
    expect($blockResult['data']['transactionCount'])->toBe(12);
    expect($blockResult['data']['validator']['address'])->toBe($validator->address);
    expect($blockResult['data']['validator']['username'])->toBe('Validator One');
    expect($blockResult['data']['validator']['isContract'])->toBeTrue();
});

it('returns transaction results including vote metadata', function () {
    Cache::tags('wallet')->flush();
    Cache::tags('identity')->flush();

    $walletCache = new WalletCache();

    $sender    = Wallet::factory()->create();
    $recipient = Wallet::factory()->create();
    $delegate  = Wallet::factory()->create();

    $token = Token::factory()->create([
        'address' => $recipient->address,
    ]);

    $senderAddress = Identity::address($sender->public_key);
    $walletCache->setWalletNameByAddress($senderAddress, 'Sender Wallet');
    $walletCache->setWalletNameByAddress($recipient->address, 'Recipient Wallet');
    $walletCache->setTokens(collect([strtolower($recipient->address) => $token]));

    $transaction = Transaction::factory()
        ->vote($delegate->address)
        ->create([
            'hash'              => str_repeat('b', 64),
            'sender_public_key' => $sender->public_key,
            'from'              => $sender->address,
            'to'                => $recipient->address,
        ]);

    $response = $this
        ->getJson(route('navbar-search.index', ['query' => $transaction->hash]))
        ->assertOk();

    $transactionResult = collect($response->json('results'))
        ->first(fn ($result) => $result['type'] === 'transaction');

    expect($transactionResult)->not->toBeNull();
    expect($transactionResult['type'])->toBe('transaction');
    expect($transactionResult['identifier'])->toBe($transaction->hash);
    expect($transactionResult['data']['hash'])->toBe($transaction->hash);
    expect($transactionResult['data']['isVote'])->toBeTrue();
    expect($transactionResult['data']['isTransfer'])->toBeFalse();
    expect($transactionResult['data']['sender']['address'])->toBe($senderAddress);
    expect($transactionResult['data']['sender']['username'])->toBe('Sender Wallet');
    expect($transactionResult['data']['recipient']['address'])->toBe($recipient->address);
    expect($transactionResult['data']['recipient']['username'])->toBe('Recipient Wallet');
    expect($transactionResult['data']['recipient']['isContract'])->toBeTrue();
    expect($transactionResult['data']['votedValidatorLabel'])->toBe(
        $delegate->username() ?? $delegate->address
    );
});

it('should search for a wallet username over a block generator', function () {
    $wallet = Wallet::factory()->create([
        'attributes' => [
            'username' => 'pieface',
        ],
    ]);
    $block = Block::factory()->create([
        'proposer' => $wallet->address,
    ]);

    Transaction::factory()
        ->transfer()
        ->create(['block_hash' => $block->hash]);

    $response = $this
        ->getJson(route('navbar-search.index', ['query' => $wallet->address]))
        ->assertOk();

    $transactionResult = collect($response->json('results'))
        ->first(fn ($result) => $result['type'] === 'wallet');

    expect($transactionResult)->not->toBeNull();
    expect($transactionResult['type'])->toBe('wallet');
    expect($transactionResult['identifier'])->toBe($wallet->address);
});

it('should search for a transaction', function () {
    Transaction::factory()
        ->transfer()
        ->create();

    $block = Block::factory()->create();

    Transaction::factory()
        ->transfer()
        ->create(['block_hash' => $block->hash]);

    $transaction = Transaction::factory()
        ->transfer()
        ->create();

    $response = $this
        ->getJson(route('navbar-search.index', ['query' => $transaction->hash]))
        ->assertOk();

    $transactionResult = collect($response->json('results'))
        ->first(fn ($result) => $result['type'] === 'transaction');

    expect($transactionResult)->not->toBeNull();
    expect($transactionResult['type'])->toBe('transaction');
    expect($transactionResult['identifier'])->toBe($transaction->hash);
});

it('should search for a block', function () {
    $block = Block::factory()->create();

    $response = $this
        ->getJson(route('navbar-search.index', ['query' => $block->hash]))
        ->assertOk();

    $transactionResult = collect($response->json('results'))
        ->first();

    expect($transactionResult)->not->toBeNull();
    expect($transactionResult['type'])->toBe('block');
    expect($transactionResult['identifier'])->toBe($block->hash);
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

    $response = $this
        ->getJson(route('navbar-search.index', ['query' => substr($wallet->address, 0, 12)]))
        ->assertOk();

    $results = collect($response->json('results'));

    $transactionResult = $results->first();

    expect($transactionResult)->not->toBeNull();
    expect($transactionResult['type'])->toBe('wallet');
    expect($transactionResult['identifier'])->toBe($wallet->address);
    expect($results->where('identifier', $otherWallet->address))->toBeEmpty();
});

it('should search only wallets when searching for an address', function () {
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
            return count($params) === 1 && $params[0] instanceof SearchQuery;
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
            ],
        ]);

    $response = $this
        ->getJson(route('navbar-search.index', ['query' => $wallet->address]))
        ->assertOk();

    $results = collect($response->json('results'));

    $transactionResult = $results->first();

    expect($transactionResult)->not->toBeNull();
    expect($transactionResult['type'])->toBe('wallet');
    expect($transactionResult['identifier'])->toBe($wallet->address);
    expect($results->where('identifier', $otherWallet->address))->toBeEmpty();
});

it('should search only transactions and blocks when searching for hash', function () {
    // Default value, overriden in phpunit.xml for the tests
    Config::set('scout.driver', 'meilisearch');

    // Mock the Meilisearch client and indexes
    $mock    = $this->mock(MeilisearchClient::class);
    $indexes = $this->mock(Indexes::class);
    $mock->shouldReceive('index')->andReturn($indexes);
    $indexes->shouldReceive('addDocuments');

    $transaction = Transaction::factory()
        ->transfer()
        ->create([
            'hash' => '01119cd018eef8c7314aed7fc3af13ec04b05ad55dd558dcc3ff7169f0af921c',
        ]);

    $this->mock(MeilisearchEngine::class)
        ->shouldReceive('multiSearch')
        ->withArgs(function ($params) {
            return count($params) === 2 &&
                collect($params)->every(fn ($param) => $param instanceof SearchQuery);
        })
        ->once()
        ->andReturn([
            'results' => [
                [
                    'indexUid' => 'transactions',
                    'hits'     => [
                        $transaction->toSearchableArray(),
                    ],
                ],
                [
                    'indexUid' => 'blocks',
                    'hits'     => [],
                ],
            ],
        ]);

    $response = $this
        ->getJson(route('navbar-search.index', ['query' => $transaction->hash]))
        ->assertOk();

    $results = collect($response->json('results'));

    $transactionResult = $results->first();

    expect($transactionResult)->not->toBeNull();
    expect($transactionResult['type'])->toBe('transaction');
    expect($transactionResult['identifier'])->toBe($transaction->hash);
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

    Config::set('arkscan.networks.development.knownWallets', $knownWalletsUrl);

    (new WalletCache())->setKnown(fn () => [
        [
            'type'    => 'team',
            'name'    => 'Alfys hot Wallet',
            'address' => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
        ],
        [
            'type'    => 'team',
            'name'    => 'other wallet',
            'address' => '0x8eD03985e78c92E4506979cAAf7671275FFd953d',
        ],
        [
            'type'    => 'team',
            'name'    => 'the alf wallet',
            'address' => '0x38b4a84773bC55e88D07cBFC76444C2A37600084',
        ],
    ]);

    $knownWallet = Wallet::factory()->create([
        'address' => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
    ]);
    $knownWallet2 = Wallet::factory()->create([
        'address' => '0x38b4a84773bC55e88D07cBFC76444C2A37600084',
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

    $response = $this
        ->getJson(route('navbar-search.index', ['query' => 'alf']))
        ->assertOk();

    $results = collect($response->json('results'));

    expect($results->firstWhere('identifier', '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B')['type'])->toBe('wallet');
    expect($results->firstWhere('identifier', '0x38b4a84773bC55e88D07cBFC76444C2A37600084')['type'])->toBe('wallet');
    expect($results->firstWhere('identifier', '0x8eD03985e78c92E4506979cAAf7671275FFd953d'))->toBeNull();
});

it('should limit to RESULT_LIMIT_PER_TYPE known wallets addresses with meilisearch', function () {
    // Default value, overriden in phpunit.xml for the tests
    Config::set('scout.driver', 'meilisearch');

    // Mock the Meilisearch client and indexes
    $mock    = $this->mock(MeilisearchClient::class);
    $indexes = $this->mock(Indexes::class);
    $mock->shouldReceive('index')->andReturn($indexes);
    $indexes->shouldReceive('addDocuments');

    $knownWalletsUrl = 'https://knownwallets.com/known-wallets.json';

    Config::set('arkscan.networks.development.knownWallets', $knownWalletsUrl);

    (new WalletCache())->setKnown(fn () => [
        [
            'type'    => 'team',
            'name'    => 'a1',
            'address' => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
        ],
        [
            'type'    => 'team',
            'name'    => 'a2',
            'address' => '0x8eD03985e78c92E4506979cAAf7671275FFd953d',
        ],
        [
            'type'    => 'team',
            'name'    => 'a3',
            'address' => '0x38b4a84773bC55e88D07cBFC76444C2A37600084',
        ],
        [
            'type'    => 'team',
            'name'    => 'a4',
            'address' => 'AZiS7KXBJ8o8JgdhPo2m4t8MGpGt1Ucxe7',
        ],
        [
            'type'    => 'team',
            'name'    => 'a5',
            'address' => 'AdS7WvzqusoP759qRo6HDmUz2L34u4fMHz',
        ],
        [
            'type'    => 'team',
            'name'    => 'a6',
            'address' => 'AKT8ji4purNoocKybdb3aHZYiVkaFimho9',
        ],
    ]);

    $knownWallet = Wallet::factory()->create([
        'address' => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
    ]);
    $knownWallet2 = Wallet::factory()->create([
        'address' => '0x8eD03985e78c92E4506979cAAf7671275FFd953d',
    ]);
    $knownWallet3 = Wallet::factory()->create([
        'address' => '0x38b4a84773bC55e88D07cBFC76444C2A37600084',
    ]);
    $knownWallet4 = Wallet::factory()->create([
        'address' => 'AZiS7KXBJ8o8JgdhPo2m4t8MGpGt1Ucxe7',
    ]);
    $knownWallet5 = Wallet::factory()->create([
        'address' => 'AdS7WvzqusoP759qRo6HDmUz2L34u4fMHz',
    ]);
    $knownWallet6 = Wallet::factory()->create([
        'address' => 'AKT8ji4purNoocKybdb3aHZYiVkaFimho9',
    ]);

    $this->mock(MeilisearchEngine::class)
        ->shouldReceive('multiSearch')
        ->withArgs(function ($params) {
            // 3 for all index types + 5 for found known wallets
            return count($params) === 8 &&
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
                        $knownWallet3->toSearchableArray(),
                        $knownWallet4->toSearchableArray(),
                        $knownWallet5->toSearchableArray(),
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

    $response = $this
        ->getJson(route('navbar-search.index', ['query' => 'a']))
        ->assertOk();

    $results = collect($response->json('results'));

    expect($results->firstWhere('identifier', '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B')['type'])->toBe('wallet');
    expect($results->firstWhere('identifier', '0x38b4a84773bC55e88D07cBFC76444C2A37600084')['type'])->toBe('wallet');
    expect($results->firstWhere('identifier', 'AKT8ji4purNoocKybdb3aHZYiVkaFimho9'))->toBeNull();
});
