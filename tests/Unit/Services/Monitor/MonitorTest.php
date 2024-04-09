<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Models\Round;
use App\Services\Monitor\Monitor;

it('should calculate the forging information', function () {
    Round::factory()->create([
        'round'        => 112168,
        'round_height' => 112168 * Network::validatorCount(),
    ]);

    expect(Monitor::roundNumber())->toBe(112168);
});

it('should calculate the height range for the given round', function ($roundNumber) {
    $round = Round::factory()->create([
        'round'        => $roundNumber,
        'round_height' => $roundNumber * Network::validatorCount(),
    ]);

    expect(Monitor::heightRangeByRound($round))->toBe([
        $round->round * Network::validatorCount(),
        ($round->round + 1) * Network::validatorCount() - 1,
    ]);
})->with([
    112168,
    112169,
    112218,
]);

it('should calculate the round number belonging to a given height', function () {
    Round::factory()->create([
        'round'        => 1,
        'round_height' => 0,
    ]);

    Round::factory()->create([
        'round'        => 2,
        'round_height' => 1 * Network::validatorCount(),
    ]);

    expect(Monitor::roundNumberFromHeight(1))->toBe(1);
    expect(Monitor::roundNumberFromHeight(Network::validatorCount() - 1))->toBe(1);
    expect(Monitor::roundNumberFromHeight(Network::validatorCount()))->toBe(2);
});
