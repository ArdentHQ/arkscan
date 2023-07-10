<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Search\TransactionSearch;
use Illuminate\Support\Collection;

it('should search for a transaction by id', function (?string $modifier) {
    $transaction = Transaction::factory(10)->create()[0];

    $result = (new TransactionSearch())->search($modifier ? $modifier($transaction->id) : $transaction->id, 5);

    expect($result)->toHaveCount(1);
})->with([null, 'strtolower', 'strtoupper']);

it('should query transaction by id', function () {
    Transaction::factory()->create([
        'id' => 'aaaaaabbbbbbbccccccdddddd',
    ]);

    Transaction::factory()->create([
        'id' => 'bbbbbbbddddd',
    ]);

    Transaction::factory()->create([
        'id' => 'ccccccdddddd',
    ]);

    expect((new TransactionSearch())->search('aaaaaa', 5))->toHaveCount(1);

    expect((new TransactionSearch())->search('bbbbbb', 5))->toHaveCount(2);

    expect((new TransactionSearch())->search('ddddd', 5))->toHaveCount(3);
});

it('limit the results', function () {
    Transaction::factory()->create([
        'id' => 'aaaaaabbbbbbbccccccdddddd',
    ]);

    Transaction::factory()->create([
        'id' => 'aaaaaabbbbbbbccccccdddddd2',
    ]);

    Transaction::factory()->create([
        'id' => 'aaaaaabbbbbbbccccccdddddd3',
    ]);

    expect((new TransactionSearch())->search('aaaaaa', 2))->toHaveCount(2);
});

it('should map meilisearch results array', function () {
    $transaction = Transaction::factory()->create();

    $result = TransactionSearch::mapMeilisearchResults([$transaction->toSearchableArray()]);

    expect($result)->toBeInstanceOf(Collection::class);

    expect($result->first())->toBeInstanceOf(Transaction::class);

    expect($result->first()->id)->toBe($transaction->id);
});

it('should produce the right meilisearch query when possibly address', function () {
    $query = TransactionSearch::buildSearchQueryForIndex('D6Z26L69gdk9qYmTv5uzk3uGepigtHY4ax', 5);

    expect($query)->toBeNull();
});

it('should produce the right meilisearch query when possibly transaction id', function () {
    $query = TransactionSearch::buildSearchQueryForIndex('75604d72872f730d7c38b9d73c916e4a532408ea0074850a581f4b28bd62acdf', 5);

    expect($query->toArray())->toMatchArray([
        'indexUid' => 'transactions',
        'filter'   => ['id = "75604d72872f730d7c38b9d73c916e4a532408ea0074850a581f4b28bd62acdf"'],
        'limit'    => 5,
    ]);
});

it('should handle spaces in search query', function () {
    $query = TransactionSearch::buildSearchQueryForIndex('a b', 5);

    expect($query->toArray())->toMatchArray([
        'indexUid' => 'transactions',
        'filter'   => ['id = "a b"'],
        'limit'    => 5,
    ]);
});

it('should handle special characters in search query', function () {
    $query = TransactionSearch::buildSearchQueryForIndex('a b \ ( "', 5);

    expect($query->toArray())->toMatchArray([
        'indexUid' => 'transactions',
        'filter'   => ['id = "a b \\\\ ( \""'],
        'limit'    => 5,
    ]);
});
