<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Facades\Rounds;
use App\Models\Block;
use App\Models\Round;
use App\Models\Wallet;
use App\Repositories\RoundRepository;
use function Tests\createRoundEntry;

beforeEach(function () {
    $this->subject = new RoundRepository();

    $wallets = Wallet::factory(Network::validatorCount())
        ->create();

    createRoundEntry(112168, 5944904, $wallets);
});

it('should get all validators for the given round', function () {
    expect($this->subject->byRound(112168)->validators)->toHaveCount(Network::validatorCount());
});

it('should get the current round', function () {
    expect($this->subject->current()->round)->toBe(Round::max('round'));
});

it('should get the slot data for the current round', function () {
    Block::factory()->create(['height' => 5944904]);

    $validators = $this->subject->validators();

    expect($validators)->toHaveCount(Network::validatorCount());
    expect(Rounds::current()->round)->toBe(112168);

    $wallet = $validators->first();

    expect($validators->firstWhere(fn ($validator) => $validator['publicKey'] === $wallet['publicKey'])['block'])->toBeNull();

    Block::factory()->create([
        'height'               => 5944905,
        'generator_public_key' => $wallet['publicKey'],
    ]);

    $validators = $this->subject->validators();

    expect($validators)->toHaveCount(Network::validatorCount());
    expect(Rounds::current()->round)->toBe(112168);

    $wallet = $validators->first();

    expect($validators->firstWhere(fn ($validator) => $validator['publicKey'] === $wallet['publicKey'])['block'])->not->toBeNull();
});
