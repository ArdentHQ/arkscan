<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Services\Monitor\RoundCalculator;

it('should calculate the round information', function () {
    $result = RoundCalculator::calculate(5719217);

    expect($result['round'])->toBe(107910);
    expect($result['roundHeight'])->toBe(5719178);
    expect($result['nextRound'])->toBe($result['round'] + 1);
    expect($result['nextRoundHeight'])->toBe($result['roundHeight'] + Network::validatorCount());
    expect($result['maxValidators'])->toBe(Network::validatorCount());
});
