<?php

declare(strict_types=1);

use App\Services\Monitor\ForgingInfoCalculator;

it('should calculate the forging information', function (int $timestamp, int $height, int $currentForger, int $nextForger, int $blockTimestamp, bool $canForge) {
    $result = ForgingInfoCalculator::calculate($timestamp, $height);

    expect($result['currentForger'])->toBe($currentForger);
    expect($result['nextForger'])->toBe($nextForger);
    expect($result['blockTimestamp'])->toBe($blockTimestamp);
    expect($result['canForge'])->toBe($canForge);
})->with([
    [114961264, 5876541, 41, 42, 114961264, true],
    [114961272, 5876542, 42, 43, 114961272, true],
    [114961280, 5876543, 43, 44, 114961280, true],
    [114961288, 5876544, 44, 45, 114961288, true],
    [114961296, 5876545, 45, 46, 114961296, true],
    [114961304, 5876546, 46, 47, 114961304, true],
    [114961312, 5876547, 47, 48, 114961312, true],
    [114961320, 5876548, 48, 49, 114961320, true],
    [114961328, 5876549, 49, 50, 114961328, true],
    [114961336, 5876550, 50, 0, 114961336, true],
    [114961344, 5876551, 0, 1, 114961344, true],
    [114961352, 5876552, 1, 2, 114961352, true],
    [114961360, 5876553, 2, 3, 114961360, true],
]);
