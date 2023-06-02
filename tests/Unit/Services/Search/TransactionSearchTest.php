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
