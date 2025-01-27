<?php

declare(strict_types=1);

use App\Models\Wallet;
use App\Services\Search\WalletSearch;
use Illuminate\Support\Collection;

it('should search for a wallet by address', function (?string $modifier) {
    $wallet = Wallet::factory(10)->create()[0];

    $result = (new WalletSearch())->search($modifier ? $modifier($wallet->address) : $wallet->address, 5);

    expect($result)->toHaveCount(1);
})->with([null, 'strtolower', 'strtoupper']);

it('should query wallet by address', function () {
    Wallet::factory()->create([
        'address' => 'aaaaaabbbbbbbccccccdddddd',
    ]);

    Wallet::factory()->create([
        'address' => 'bbbbbbbddddd',
    ]);

    Wallet::factory()->create([
        'address' => 'ccccccdddddd',
    ]);

    expect((new WalletSearch())->search('aaaaaa', 5))->toHaveCount(1);

    expect((new WalletSearch())->search('bbbbbb', 5))->toHaveCount(2);

    expect((new WalletSearch())->search('ddddd', 5))->toHaveCount(3);
});

it('limit the results', function () {
    Wallet::factory()->create([
        'address' => 'aaaaaabbbbbbbccccccdddddd',
    ]);

    Wallet::factory()->create([
        'address' => 'aaaaaabbbbbbbccccccdddddd2',
    ]);

    Wallet::factory()->create([
        'address' => 'aaaaaabbbbbbbccccccdddddd3',
    ]);

    expect((new WalletSearch())->search('aaaaaa', 2))->toHaveCount(2);
});

it('should map meilisearch results array', function () {
    $wallet = Wallet::factory()->create();

    $result = WalletSearch::mapMeilisearchResults([$wallet->toSearchableArray()]);

    expect($result)->toBeInstanceOf(Collection::class);

    expect($result->first())->toBeInstanceOf(Wallet::class);

    expect($result->first()->address)->toBe($wallet->address);
});

it('should handle a negative limit', function () {
    $query = WalletSearch::buildSearchQueryForIndex('aaaaaabbbbbbbccccccdddddd3', -5);

    expect($query->toArray())->toMatchArray([
        'indexUid' => 'wallets',
        'q'        => 'aaaaaabbbbbbbccccccdddddd3',
    ]);
});
