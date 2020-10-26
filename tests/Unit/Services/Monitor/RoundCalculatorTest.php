<?php

declare(strict_types=1);

use App\Services\Monitor\RoundCalculator;

it('should calculate the round information', function () {
    $result = RoundCalculator::calculate(100);

    expect($result['round'])->toBe(1);
    expect($result['roundHeight'])->toBe(51);
    expect($result['nextRound'])->toBe(2);
    expect($result['nextRoundHeight'])->toBe(102);
    expect($result['maxDelegates'])->toBe(51);
});
