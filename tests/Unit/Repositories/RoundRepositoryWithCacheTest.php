<?php

declare(strict_types=1);

// @TODO: assert that cache has been called

use App\Models\Round;
use App\Models\Wallet;
use App\Repositories\RoundRepository;
use App\Repositories\RoundRepositoryWithCache;

beforeEach(fn () => $this->subject = new RoundRepositoryWithCache(new RoundRepository()));

it('should get all delegates for the given round', function () {
    Wallet::factory(51)->create()->each(function ($wallet) {
        Round::factory()->create([
            'round'      => '112168',
            'public_key' => $wallet->public_key,
        ]);
    });

    expect($this->subject->allByRound(112168))->toHaveCount(51);
});

it('should get the current round', function () {
    Round::factory()->create();

    expect($this->subject->currentRound())->toBeInstanceOf(Round::class);
});
