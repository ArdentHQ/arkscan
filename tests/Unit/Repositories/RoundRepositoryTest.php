<?php

declare(strict_types=1);

use App\Facades\Rounds;
use App\Models\Block;
use App\Models\Round;
use App\Models\Wallet;
use App\Repositories\RoundRepository;

beforeEach(function () {
    $this->subject = new RoundRepository();

    Wallet::factory(51)->create()->each(function ($wallet) {
        Round::factory()->create([
            'round'      => '112168',
            'public_key' => $wallet->public_key,
        ]);
    });
});

it('should get all validators for the given round', function () {
    expect($this->subject->byRound(112168))->toHaveCount(51);
});

it('should get the current round', function () {
    expect($this->subject->current())->toBe(Round::max('round'));
});

it('should get the slot data for the current round', function () {
    Block::factory()->create(['height' => 5720518]);

    $validators = $this->subject->validators();

    expect($validators)->toHaveCount(51);
    expect(Rounds::current())->toBe(112168);

    $wallet = $validators->first();

    expect($validators->firstWhere(fn ($validator) => $validator['publicKey'] === $wallet['publicKey'])['block'])->toBeNull();

    Block::factory()->create([
        'height'               => 5720519,
        'generator_public_key' => $wallet['publicKey'],
    ]);

    $validators = $this->subject->validators();

    expect($validators)->toHaveCount(51);
    expect(Rounds::current())->toBe(112168);

    $wallet = $validators->first();

    expect($validators->firstWhere(fn ($validator) => $validator['publicKey'] === $wallet['publicKey'])['block'])->not->toBeNull();
});
