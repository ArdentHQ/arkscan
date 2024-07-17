<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Facades\Rounds;
use App\Services\Monitor\ValidatorTracker;
use Carbon\Carbon;
use function Tests\createPartialRound;
use function Tests\createRealisticRound;
use function Tests\createRoundEntry;
use function Tests\getRoundValidators;

it('should calculate the forging order', function () {
    $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

    $this->freezeTime();

    [$validators, $round, $height] = createRealisticRound([
        array_fill(0, 53, true),
        array_fill(0, 53, true),
    ], $this);

    $roundHeight = Rounds::current()->round_height;

    $order = ValidatorTracker::execute($validators->pluck('public_key')->toArray(), $roundHeight);

    expect($order)->toHaveCount(Network::validatorCount());

    $publicKeys = $validators->pluck('public_key');

    foreach ($order as $validator) {
        expect($publicKeys)->toContain($validator['publicKey']);
    }
});

it('should handle no missed block', function () {
    $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

    $this->freezeTime();

    [$validators, $round, $height] = createRealisticRound([
        array_fill(0, 53, true),
        array_fill(0, 53, true),
    ], $this);

    createPartialRound(
        $round,
        $height,
        null,
        $this,
        [],
        [],
        true,
        51
    );

    $roundHeight = Rounds::current()->round_height;

    $order = ValidatorTracker::execute($validators->pluck('public_key')->toArray(), $roundHeight);

    expect(collect($order)->where('status', 'pending')->count())->toBe(2);
});

it('should handle one missed block', function () {
    $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

    $this->freezeTime();

    [$validators, $round, $height] = createRealisticRound([
        array_fill(0, 53, true),
        [
            ...array_fill(0, 4, true),
            false,
            ...array_fill(0, 48, true),
        ],
    ], $this);

    for ($i = 0; $i < 3; $i++) {
        createRoundEntry($round, $height, $validators);
        $validatorsOrder = getRoundValidators(false, $round);
        $validatorIndex  = $validatorsOrder->search(fn ($validator) => $validator['publicKey'] === $validators->get(4)->public_key);
        if ($validatorIndex < 51) {
            break;
        }

        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
        ], $this);
    }

    createPartialRound(
        $round,
        $height,
        null,
        $this,
        [
            $validators->get(4)->public_key,
        ],
        [
            $validators->get(4)->public_key,
        ],
        true,
        51,
    );

    $roundHeight = Rounds::current()->round_height;

    $order = ValidatorTracker::execute($validators->pluck('public_key')->toArray(), $roundHeight);

    expect(collect($order)->where('status', 'pending')->count())->toBe(2);
});

it('should handle missed blocks', function () {
    $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

    $this->freezeTime();

    [$validators, $round, $height] = createRealisticRound([
        array_fill(0, 53, true),
        [
            ...array_fill(0, 4, true),
            ...array_fill(0, 5, false),
            ...array_fill(0, 44, true),
        ],
    ], $this);

    for ($i = 0; $i < 3; $i++) {
        createRoundEntry($round, $height, $validators);
        $validatorsOrder = getRoundValidators(false, $round);
        foreach (range(4, 8) as $index) {
            $validatorIndex = $validatorsOrder->search(fn ($validator) => $validator['publicKey'] === $validators->get($index)->public_key);
            if ($validatorIndex < 51) {
                break 2;
            }
        }

        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
            [
                ...array_fill(0, 4, true),
                ...array_fill(0, 5, false),
                ...array_fill(0, 44, true),
            ],
        ], $this);
    }

    createPartialRound(
        $round,
        $height,
        null,
        $this,
        [
            $validators->get(4)->public_key,
            $validators->get(5)->public_key,
            $validators->get(6)->public_key,
            $validators->get(7)->public_key,
            $validators->get(8)->public_key,
        ],
        [
            $validators->get(4)->public_key,
            $validators->get(5)->public_key,
            $validators->get(6)->public_key,
            $validators->get(7)->public_key,
            $validators->get(8)->public_key,
        ],
        true,
        51,
    );

    $roundHeight = Rounds::current()->round_height;

    $order = ValidatorTracker::execute($validators->pluck('public_key')->toArray(), $roundHeight);

    expect(collect($order)->where('status', 'pending')->count())->toBe(2);
});
