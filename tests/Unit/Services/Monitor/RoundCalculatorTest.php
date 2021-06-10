<?php

declare(strict_types=1);

use App\Services\Monitor\RoundCalculator;

it('should calculate the round information', function () {
    $result = RoundCalculator::calculate(5719217);

    expect($result['round'])->toBe(112142);
    expect($result['roundHeight'])->toBe(5719192);
    expect($result['nextRound'])->toBe(112143);
    expect($result['nextRoundHeight'])->toBe(5719243);
    expect($result['maxDelegates'])->toBe(51);
});
