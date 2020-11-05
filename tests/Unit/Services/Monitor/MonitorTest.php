<?php

declare(strict_types=1);

use App\Services\Monitor\Monitor;

it('should calculate the forging information', function () {
    expect(Monitor::roundNumber())->toBeInt();
});

it('should calculate the height range for the given round', function () {
    expect(Monitor::heightRangeByRound(50))->toBe([2499, 2549]);
});
