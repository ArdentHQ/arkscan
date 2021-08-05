<?php

declare(strict_types=1);

// @TODO: assert that cache has been called

use App\Models\Round;
use App\Models\Wallet;
use App\Repositories\RoundRepository;
use App\Repositories\RoundRepositoryWithCache;

beforeEach(function () {
    $this->subject = new RoundRepositoryWithCache(new RoundRepository());

    Wallet::factory(51)->create()->each(function ($wallet) {
        Round::factory()->create([
            'round'      => '112168',
            'public_key' => $wallet->public_key,
        ]);
    });
});

it('should get all delegates for the given round', function () {
    expect($this->subject->allByRound(112168))->toHaveCount(51);
});

it('should get the current round', function () {
    expect($this->subject->current())->toBe(Round::max('round'));
});
