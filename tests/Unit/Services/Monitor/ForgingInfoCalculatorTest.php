<?php

declare(strict_types=1);

use App\Services\Monitor\ForgingInfoCalculator;

it('should calculate the forging information', function () {
    $result = ForgingInfoCalculator::calculate(113620904, 5720529);

    expect($result['currentForger'])->toBe(31);
    expect($result['nextForger'])->toBe(32);
    expect($result['blockTimestamp'])->toBe(113620904);
    expect($result['canForge'])->toBeTrue();
});
