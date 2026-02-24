<?php

declare(strict_types=1);

use App\DTO\Inertia\ForgingStats as ForgingStatsDTO;
use App\DTO\Inertia\Wallet as WalletDTO;
use App\Models\ForgingStats;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;

it('should make an instance with a validator', function () {
    $wallet = Wallet::factory()->activeValidator()->create([
        'balance'    => 100 * 1e18,
        'attributes' => [
            'username'              => 'validator1',
            'validatorPublicKey'    => 'some-public-key',
            'validatorVoteBalance'  => 500 * 1e18,
        ],
    ]);

    (new WalletCache())->setVoterCount($wallet->address, 10);

    $forgingStats = ForgingStats::factory()->create([
        'address'       => $wallet->address,
        'missed_height' => 54321,
        'timestamp'     => 1490103134,
    ]);

    $subject = ForgingStatsDTO::fromModel($forgingStats);

    expect($subject->number)->toBe(54321);
    expect($subject->timestamp)->toBe(1490103134);
    expect($subject->validator)->toBeInstanceOf(WalletDTO::class);
    expect($subject->validator->address)->toBe($wallet->address);
    expect($subject->voterCount)->toBe(10);
    expect($subject->votesPercentage)->toBeFloat();
    expect($subject->votes)->toBeFloat();
});

it('should handle missing validator', function () {
    $forgingStats = ForgingStats::factory()->create([
        'address'       => 'address-to-missing-validator',
        'missed_height' => 12345,
        'timestamp'     => 1490103134,
    ]);

    $subject = ForgingStatsDTO::fromModel($forgingStats);

    expect($subject->number)->toBe(12345);
    expect($subject->timestamp)->toBe(1490103134);
    expect($subject->validator)->toBeNull();
    expect($subject->voterCount)->toBeNull();
    expect($subject->votesPercentage)->toBeNull();
    expect($subject->votes)->toBeNull();
});
