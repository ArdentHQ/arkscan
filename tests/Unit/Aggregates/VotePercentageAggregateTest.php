<?php

declare(strict_types=1);

use App\Aggregates\VotePercentageAggregate;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;

beforeEach(function () {
});

it('should aggregate and format', function () {
    (new NetworkCache())->setSupply(fn () => '13628098200000000');

    $wallet  = Wallet::factory()->create();
    $wallet2 = Wallet::factory()->create([
        'balance' => '10000000000000000',

        'attributes' => [
            'vote' => $wallet->public_key,
        ],
    ]);

    $aggregate = (new VotePercentageAggregate())->aggregate();

    expect($aggregate)->toBeString();
    expect(number_format((float) $aggregate, 12))->toBe('73.377809972047');
    expect(number_format((float) $aggregate, 12))->toBe(number_format(10000000000000000 / 13628098200000000 * 100, 12));
});

it('should return zero if no vote balance', function () {
    (new NetworkCache())->setSupply(fn () => '13628098200000000');

    $aggregate = (new VotePercentageAggregate())->aggregate();

    expect($aggregate)->toBeString();
    expect($aggregate)->toBe('0');
});

it('should return zero if no supply', function () {
    (new NetworkCache())->setSupply(fn () => '0');

    $aggregate = (new VotePercentageAggregate())->aggregate();

    expect($aggregate)->toBeString();
    expect($aggregate)->toBe('0');
});
