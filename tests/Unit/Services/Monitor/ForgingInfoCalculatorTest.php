<?php

declare(strict_types=1);

use App\Services\Monitor\ForgingInfoCalculator;

it('should calculate the forging information', function (int $height, int $currentForger, int $nextForger, int $roundHeight) {
    $result = ForgingInfoCalculator::calculate($roundHeight, $height);

    expect($result['currentForger'])->toBe($currentForger);
    expect($result['nextForger'])->toBe($nextForger);
})->with([
    [5876541, 41, 42, 5876500],
    [5876542, 42, 43, 5876500],
    [5876543, 43, 44, 5876500],
    [5876544, 44, 45, 5876500],
    [5876545, 45, 46, 5876500],
    [5876546, 46, 47, 5876500],
    [5876547, 47, 48, 5876500],
    [5876548, 48, 49, 5876500],
    [5876549, 49, 50, 5876500],
    [5876550, 50, 51, 5876500],
    [5876551, 0, 1, 5876551],
    [5876552, 1, 2, 5876551],
    [5876553, 2, 3, 5876551],
]);
