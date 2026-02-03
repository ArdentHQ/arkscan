<?php

declare(strict_types=1);

use App\DTO\Statistics\AddressHoldingStatistics;

it('should create statistics object correctly', function () {
    $subject = AddressHoldingStatistics::make([
        [
            'count'   => 2,
            'grouped' => 1,
        ],
        [
            'count'   => 2000,
            'grouped' => 1000,
        ],
        [
            'count'   => 20000,
            'grouped' => 10000,
        ],
        [
            'count'   => 200000,
            'grouped' => 100000,
        ],
        [
            'count'   => 2000000,
            'grouped' => 1000000,
        ],
    ]);

    expect($subject->greaterThanOne)->toBe(2);
    expect($subject->greaterThanOneThousand)->toBe(2000);
    expect($subject->greaterThanTenThousand)->toBe(20000);
    expect($subject->greaterThanOneHundredThousand)->toBe(200000);
    expect($subject->greaterThanOneMillion)->toBe(2000000);
});
