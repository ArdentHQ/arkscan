<?php

declare(strict_types=1);

use App\Models\Round;
use App\Services\Monitor\Monitor;

beforeEach(function () {
    Round::factory()->create([
        'round'      => '112168',
    ]);
});

it('should calculate the forging information', function () {
    expect(Monitor::roundNumber())->toBeInt();
});

it('should calculate the height range for the given round', function () {
    expect(Monitor::heightRangeByRound(1))->toBe([1, 51]);
    expect(Monitor::heightRangeByRound(2))->toBe([52, 102]);
    expect(Monitor::heightRangeByRound(50))->toBe([2500, 2550]);
});

it('should calculate the round number belonging to a given height', function () {
    expect(Monitor::roundNumberFromHeight(1))->toBe(1);
    expect(Monitor::roundNumberFromHeight(51))->toBe(1);
    expect(Monitor::roundNumberFromHeight(52))->toBe(2);
});
