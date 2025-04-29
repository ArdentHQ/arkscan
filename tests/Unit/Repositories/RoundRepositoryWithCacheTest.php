<?php

declare(strict_types=1);

// @TODO: assert that cache has been called

use App\Facades\Network;
use App\Facades\Rounds;
use App\Models\Block;
use App\Models\Round;
use App\Models\Wallet;
use App\Repositories\RoundRepository;
use App\Repositories\RoundRepositoryWithCache;
use function Tests\createRoundEntry;

beforeEach(function () {
    $this->subject = new RoundRepositoryWithCache(new RoundRepository());

    $wallets = Wallet::factory(Network::validatorCount())->create();

    createRoundEntry(112168, (112168 - 1) * Network::validatorCount(), $wallets);
});

it('should get all validators for the given round', function () {
    expect($this->subject->byRound(112168)->validators)->toHaveCount(Network::validatorCount());
});

it('should get the current round', function () {
    expect($this->subject->current()->round)->toBe(Round::max('round'));
});

it('should get the slot data for the current round using cache', function () {
    Block::factory()->create(['number' => 5944852]);

    $validators = $this->subject->validators();

    expect($validators)->toHaveCount(Network::validatorCount());
    expect(Rounds::current()->round)->toBe(112168);

    $wallet = $validators->first();

    expect($validators->firstWhere(fn ($validator) => $validator['address'] === $wallet['address'])['block'])->toBeNull();

    Block::factory()->create([
        'number'               => 5944853,
        'proposer'             => $wallet['address'],
    ]);

    $validators = $this->subject->validators();

    expect($validators)->toHaveCount(Network::validatorCount());
    expect(Rounds::current()->round)->toBe(112168);

    expect($validators->firstWhere(fn ($validator) => $validator['address'] === $wallet['address'])['block'])->toBeNull();

    $wallet = $validators->first();

    $this->travel(8)->seconds();

    $validators = $this->subject->validators();

    expect($validators->firstWhere(fn ($validator) => $validator['address'] === $wallet['address'])['block'])->not->toBeNull();
});
